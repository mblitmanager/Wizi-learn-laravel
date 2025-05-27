import { Chart, registerables } from "chart.js";
import {
    ChoroplethController,
    GeoFeature,
    ColorScale,
    ProjectionScale,
} from "chartjs-chart-geo";

import * as d3 from "d3";
import { feature } from "topojson-client"; // remplace TopojsonFeature

Chart.register(
    ...registerables,
    ChoroplethController,
    GeoFeature,
    ColorScale,
    ProjectionScale
);

document.addEventListener("DOMContentLoaded", async () => {
    const ctx = document.getElementById("loginMap");

    if (!ctx) return;

    try {
        const worldAtlasUrl =
            "https://unpkg.com/world-atlas@2.0.2/countries-50m.json";
        const statsUrl = "/administrateur/dashboard/stats-login";

        const [topoData, statsResponse] = await Promise.all([
            fetch(worldAtlasUrl).then((res) => res.json()),
            fetch(statsUrl).then((res) => res.json()),
        ]);

        const countries = feature(
            topoData,
            topoData.objects.countries
        ).features;
        const loginStats = statsResponse.map_data || [];

        const getValueByCode = (countryFeature) => {
            const countryName = countryFeature.properties.name;
            const entry = loginStats.find(
                (item) =>
                    item.country &&
                    countryName &&
                    item.country.toLowerCase() === countryName.toLowerCase()
            );
            return entry ? entry.value : 0;
        };

        new Chart(ctx.getContext("2d"), {
            type: "choropleth",
            data: {
                labels: countries.map((d) => d.properties.name),
                datasets: [
                    {
                        label: "Nombre de connexions",
                        data: countries.map((d) => ({
                            feature: d,
                            value: getValueByCode(d),
                            name: d.properties.name,
                        })),
                    },
                ],
            },
            options: {
                showOutline: true,
                showGraticule: true,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const countryName =
                                    ctx.raw.feature.properties.name;
                                const stat = loginStats.find(
                                    (item) =>
                                        item.country &&
                                        countryName &&
                                        item.country.toLowerCase() ===
                                            countryName.toLowerCase()
                                );
                                return stat
                                    ? `${stat.country}: ${stat.value} connexions` +
                                          (stat.last_activity
                                              ? `\nDernière activité: ${stat.last_activity}`
                                              : "")
                                    : `${countryName}: 0 connexions`;
                            },
                        },
                    },
                },
                scales: {
                    projection: {
                        axis: "x",
                        projection: "equalEarth",
                    },
                    color: {
                        axis: "color",
                        interpolator: d3.interpolateYlGnBu,
                        legend: {
                            position: "bottom-right",
                        },
                    },
                },
            },
        });
    } catch (error) {
        console.error("Error loading map:", error);
        alert("Erreur lors du chargement de la carte.");
    }
});
