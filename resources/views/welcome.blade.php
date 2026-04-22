<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Environmental Overview Dashboard</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            /* Dark Mode Palette (Default) */
            --bg-color: #0B0F19;
            --card-bg: rgba(22, 33, 62, 0.6);
            --card-bg-hover: rgba(30, 58, 138, 0.4);
            --card-border: rgba(255, 255, 255, 0.05);
            --text-main: #FFFFFF;
            --text-muted: #94A3B8;
            --title-gradient-start: #ffffff;
            --title-gradient-end: #93c5fd;
            --accent-blue: #3B82F6;
            --accent-bg: rgba(59, 130, 246, 0.1);
            --accent-glow: rgba(59, 130, 246, 0.15);
            --status-normal: #FFFFFF;
            --status-critical: #EF4444;
            --critical-bg: rgba(239, 68, 68, 0.1);
            --bg-gradient-1: rgba(30, 58, 138, 0.15);
            --bg-gradient-2: rgba(15, 23, 42, 0.8);
            --tooltip-bg: rgba(15, 23, 42, 0.9);
            --tooltip-text: #FFFFFF;
            --chart-grid: rgba(255, 255, 255, 0.05);
            --btn-bg: rgba(255, 255, 255, 0.1);
            --btn-hover: rgba(255, 255, 255, 0.2);
        }

        body.light-mode {
            /* Light Mode Palette */
            --bg-color: #F1F5F9;
            --card-bg: rgba(255, 255, 255, 0.8);
            --card-bg-hover: rgba(255, 255, 255, 1);
            --card-border: rgba(0, 0, 0, 0.05);
            --text-main: #0F172A;
            --text-muted: #64748B;
            --title-gradient-start: #1E3A8A;
            --title-gradient-end: #3B82F6;
            --accent-blue: #2563EB;
            --accent-bg: rgba(37, 99, 235, 0.1);
            --accent-glow: rgba(37, 99, 235, 0.15);
            --status-normal: #10B981; /* Green instead of white for better visibility */
            --status-critical: #EF4444;
            --critical-bg: rgba(239, 68, 68, 0.1);
            --bg-gradient-1: rgba(59, 130, 246, 0.1);
            --bg-gradient-2: rgba(241, 245, 249, 0.8);
            --tooltip-bg: rgba(255, 255, 255, 0.95);
            --tooltip-text: #0F172A;
            --chart-grid: rgba(0, 0, 0, 0.05);
            --btn-bg: rgba(0, 0, 0, 0.05);
            --btn-hover: rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            background-image: radial-gradient(circle at top right, var(--bg-gradient-1), transparent 40%),
                              radial-gradient(circle at bottom left, var(--bg-gradient-2), transparent 40%);
            background-attachment: fixed;
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem;
            line-height: 1.5;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Controls: Theme & Language */
        .top-controls {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-bottom: -3rem;
            position: relative;
            z-index: 10;
            animation: fadeInDown 0.8s ease-out;
        }

        .control-btn {
            background: var(--btn-bg);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .control-btn:hover {
            background: var(--btn-hover);
        }

        .control-btn i {
            font-size: 1.25rem;
        }

        /* Header Section */
        .header {
            margin-bottom: 2.5rem;
            animation: fadeInDown 0.8s ease-out;
        }

        .title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, var(--title-gradient-start), var(--title-gradient-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.25rem;
        }

        .subtitle {
            font-size: 0.875rem;
            font-weight: 600;
            color: #10B981; /* Emerald green for active status */
            letter-spacing: 1.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-transform: uppercase;
        }

        .subtitle::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #10B981;
            border-radius: 50%;
            box-shadow: 0 0 10px #10B981;
            animation: pulse 2s infinite;
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        /* Hero Card */
        .hero-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: 1.25rem;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
            /* Don't transition transform to prevent jitter, handle in hover */
            transition: box-shadow 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
        }

        .hero-card:nth-child(1) { animation-delay: 0.1s; }
        .hero-card:nth-child(2) { animation-delay: 0.2s; }
        .hero-card:nth-child(3) { animation-delay: 0.3s; }
        .hero-card:nth-child(4) { animation-delay: 0.4s; }

        .hero-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px var(--accent-glow);
            background: var(--card-bg-hover);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .card-icon {
            font-size: 2rem;
            color: var(--accent-blue);
            background: var(--accent-bg);
            padding: 0.75rem;
            border-radius: 1rem;
        }

        .card-icon.critical-icon {
            color: var(--status-critical);
            background: var(--critical-bg);
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            box-shadow: 0 0 10px currentColor;
        }

        .status-dot.normal {
            background-color: var(--status-normal);
            color: var(--status-normal);
        }

        .status-dot.critical {
            background-color: var(--status-critical);
            color: var(--status-critical);
            animation: flash 1.5s infinite;
        }

        .card-title {
            font-size: 1rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .card-value {
            font-family: 'Outfit', sans-serif;
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
        }

        .card-unit {
            font-size: 1rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .card-desc {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .desc-optimal { color: #10B981; } /* Emerald */
        .desc-stable { color: var(--accent-blue); } /* Blue */
        .desc-critical { color: var(--status-critical); } /* Red */

        /* Chart Section */
        .chart-section {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: 1.25rem;
            padding: 2rem;
            animation: fadeInUp 0.8s ease-out 0.6s forwards;
            opacity: 0;
            position: relative;
        }

        .chart-header {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .chart-title-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .chart-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .chart-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--accent-bg);
            padding: 0.4rem 1rem;
            border-radius: 2rem;
            border: 1px solid var(--accent-glow);
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--accent-blue);
        }

        .chart-badge-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent-blue);
            border-radius: 50%;
        }

        .chart-subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }

        /* Animations */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        @keyframes flash {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .cards-grid { grid-template-columns: 1fr; }
            .title { font-size: 2rem; }
            .top-controls { margin-bottom: 1rem; justify-content: flex-start; }
            .chart-title-wrapper { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .chart-badge { align-self: flex-start; }
            .chart-container { height: 300px; }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- Controls -->
        <div class="top-controls">
            <button id="lang-toggle" class="control-btn" title="Translate (EN/ID)">
                <i class="ph ph-translate"></i> <span id="lang-text">ID</span>
            </button>
            <button id="theme-toggle" class="control-btn" title="Toggle Theme">
                <i class="ph ph-sun" id="theme-icon"></i> <span id="theme-text">Light</span>
            </button>
        </div>

        <!-- Header -->
        <header class="header">
            <h1 class="title" id="t-title">Environmental Overview</h1>
            <div class="subtitle" id="t-subtitle">REAL-TIME MONITORING ACTIVE</div>
        </header>

        <!-- Cards Section -->
        <div class="cards-grid">
            <!-- Card 1: Air Quality -->
            <div class="hero-card" id="card-aq">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="ph ph-gauge"></i>
                    </div>
                    <div class="status-dot normal" title="Normal"></div>
                </div>
                <div class="card-title" id="t-aq-title">Air Quality</div>
                <div class="card-value">
                    <span id="val-aq">45.2</span>
                    <span class="card-unit">PPM</span>
                </div>
                <div class="card-desc desc-optimal" id="t-aq-desc">Optimal Range</div>
            </div>

            <!-- Card 2: Temperature -->
            <div class="hero-card" id="card-temp">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="ph ph-thermometer"></i>
                    </div>
                    <div class="status-dot normal" title="Normal"></div>
                </div>
                <div class="card-title" id="t-temp-title">Temperature</div>
                <div class="card-value">
                    <span id="val-temp">22.5</span>
                    <span class="card-unit">°C</span>
                </div>
                <div class="card-desc desc-stable" id="t-temp-desc">Stable Ambient</div>
            </div>

            <!-- Card 3: Humidity -->
            <div class="hero-card" id="card-hum">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="ph ph-drop"></i>
                    </div>
                    <div class="status-dot normal" title="Normal"></div>
                </div>
                <div class="card-title" id="t-hum-title">Humidity</div>
                <div class="card-value">
                    <span id="val-hum">62</span>
                    <span class="card-unit">%</span>
                </div>
                <div class="card-desc desc-optimal" id="t-hum-desc">Optimal Range</div>
            </div>

            <!-- Card 4: CO2 Levels -->
            <div class="hero-card" id="card-co2" data-critical="true">
                <div class="card-header">
                    <div class="card-icon critical-icon">
                        <i class="ph ph-warning-octagon"></i>
                    </div>
                    <div class="status-dot critical" title="Critical"></div>
                </div>
                <div class="card-title" id="t-co2-title">CO2 Levels</div>
                <div class="card-value">
                    <span id="val-co2">850</span>
                    <span class="card-unit">PPM</span>
                </div>
                <div class="card-desc desc-critical" id="t-co2-desc">Critical Alert</div>
            </div>
        </div>

        <!-- Chart Section -->
        <section class="chart-section">
            <div class="chart-header">
                <div class="chart-title-wrapper">
                    <h2 class="chart-title" id="t-chart-title">24-Hour Air Quality Trend</h2>
                    <div class="chart-badge">
                        <div class="chart-badge-dot"></div>
                        <span>PM 2.5</span>
                    </div>
                </div>
                <div class="chart-subtitle" id="t-sensor">SENSOR NODE: REDESMA-01</div>
            </div>
            
            <div class="chart-container">
                <canvas id="airQualityChart"></canvas>
            </div>
        </section>
    </div>

    <!-- Script for Dynamic Content & Chart -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup Chart.js with dummy data
            const ctx = document.getElementById('airQualityChart').getContext('2d');
            
            // Function to get CSS variables for chart colors
            function getChartColors() {
                const isLight = document.body.classList.contains('light-mode');
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, isLight ? 'rgba(37, 99, 235, 0.4)' : 'rgba(59, 130, 246, 0.5)'); 
                gradient.addColorStop(1, isLight ? 'rgba(37, 99, 235, 0.0)' : 'rgba(59, 130, 246, 0.0)');
                
                return {
                    gradient,
                    borderColor: isLight ? '#2563EB' : '#3B82F6',
                    pointBg: isLight ? '#F1F5F9' : '#0B0F19',
                    gridColor: isLight ? 'rgba(0,0,0,0.05)' : 'rgba(255,255,255,0.05)',
                    textColor: isLight ? '#64748B' : '#94A3B8',
                    tooltipBg: isLight ? 'rgba(255,255,255,0.95)' : 'rgba(15,23,42,0.9)',
                    tooltipText: isLight ? '#0F172A' : '#FFFFFF'
                };
            }

            // Dummy data generation
            const hours = Array.from({length: 24}, (_, i) => `${i}:00`);
            const dataPoints = [42, 45, 48, 50, 47, 44, 43, 41, 39, 38, 40, 42, 45, 49, 52, 55, 53, 50, 48, 46, 44, 45, 46, 45];

            let chartColors = getChartColors();

            let airQualityChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: hours,
                    datasets: [{
                        label: 'PM 2.5 (PPM)',
                        data: dataPoints,
                        borderColor: chartColors.borderColor,
                        backgroundColor: chartColors.gradient,
                        borderWidth: 3,
                        pointBackgroundColor: chartColors.pointBg,
                        pointBorderColor: chartColors.borderColor,
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4 // Smooth curves
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: chartColors.tooltipBg,
                            titleColor: chartColors.tooltipText,
                            bodyColor: chartColors.textColor,
                            borderColor: 'rgba(128,128,128,0.2)',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `${context.parsed.y} PPM`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: chartColors.gridColor,
                                drawBorder: false,
                            },
                            ticks: {
                                color: chartColors.textColor,
                                padding: 10,
                                font: { family: "'Inter', sans-serif", size: 11 }
                            }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: {
                                color: chartColors.textColor,
                                maxTicksLimit: 12,
                                padding: 10,
                                font: { family: "'Inter', sans-serif", size: 11 }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                }
            });

            // ================= THEME TOGGLE =================
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const themeText = document.getElementById('theme-text');

            themeToggleBtn.addEventListener('click', () => {
                document.body.classList.toggle('light-mode');
                const isLight = document.body.classList.contains('light-mode');
                
                // Update button UI
                if (isLight) {
                    themeIcon.className = 'ph ph-moon';
                    themeText.textContent = currentLang === 'en' ? 'Dark' : 'Gelap';
                } else {
                    themeIcon.className = 'ph ph-sun';
                    themeText.textContent = currentLang === 'en' ? 'Light' : 'Terang';
                }

                // Update Chart Colors
                chartColors = getChartColors();
                airQualityChart.data.datasets[0].borderColor = chartColors.borderColor;
                airQualityChart.data.datasets[0].backgroundColor = chartColors.gradient;
                airQualityChart.data.datasets[0].pointBackgroundColor = chartColors.pointBg;
                airQualityChart.data.datasets[0].pointBorderColor = chartColors.borderColor;
                airQualityChart.options.scales.x.ticks.color = chartColors.textColor;
                airQualityChart.options.scales.y.ticks.color = chartColors.textColor;
                airQualityChart.options.scales.y.grid.color = chartColors.gridColor;
                airQualityChart.options.plugins.tooltip.backgroundColor = chartColors.tooltipBg;
                airQualityChart.options.plugins.tooltip.titleColor = chartColors.tooltipText;
                airQualityChart.options.plugins.tooltip.bodyColor = chartColors.textColor;
                airQualityChart.update();
            });

            // ================= TRANSLATION =================
            const translations = {
                en: {
                    theme_light: "Light",
                    theme_dark: "Dark",
                    title: "Environmental Overview",
                    subtitle: "REAL-TIME MONITORING ACTIVE",
                    aq_title: "Air Quality",
                    aq_desc: "Optimal Range",
                    temp_title: "Temperature",
                    temp_desc: "Stable Ambient",
                    hum_title: "Humidity",
                    hum_desc: "Optimal Range",
                    co2_title: "CO2 Levels",
                    co2_desc_critical: "Critical Alert",
                    co2_desc_stable: "Stable Range",
                    chart_title: "24-Hour Air Quality Trend",
                    sensor: "SENSOR NODE: REDESMA-01"
                },
                id: {
                    theme_light: "Terang",
                    theme_dark: "Gelap",
                    title: "Ikhtisar Lingkungan",
                    subtitle: "PEMANTAUAN REAL-TIME AKTIF",
                    aq_title: "Kualitas Udara",
                    aq_desc: "Rentang Optimal",
                    temp_title: "Suhu",
                    temp_desc: "Suhu Stabil",
                    hum_title: "Kelembapan",
                    hum_desc: "Rentang Optimal",
                    co2_title: "Kadar CO2",
                    co2_desc_critical: "Peringatan Kritis",
                    co2_desc_stable: "Rentang Stabil",
                    chart_title: "Tren Kualitas Udara 24 Jam",
                    sensor: "TITIK SENSOR: REDESMA-01"
                }
            };

            let currentLang = 'en';
            const langToggleBtn = document.getElementById('lang-toggle');
            const langText = document.getElementById('lang-text');

            langToggleBtn.addEventListener('click', () => {
                currentLang = currentLang === 'en' ? 'id' : 'en';
                
                // Update Button Text
                langText.textContent = currentLang === 'en' ? 'ID' : 'EN';
                
                // Apply Translations
                document.getElementById('t-title').textContent = translations[currentLang].title;
                document.getElementById('t-subtitle').textContent = translations[currentLang].subtitle;
                document.getElementById('t-aq-title').textContent = translations[currentLang].aq_title;
                document.getElementById('t-aq-desc').textContent = translations[currentLang].aq_desc;
                document.getElementById('t-temp-title').textContent = translations[currentLang].temp_title;
                document.getElementById('t-temp-desc').textContent = translations[currentLang].temp_desc;
                document.getElementById('t-hum-title').textContent = translations[currentLang].hum_title;
                document.getElementById('t-hum-desc').textContent = translations[currentLang].hum_desc;
                document.getElementById('t-co2-title').textContent = translations[currentLang].co2_title;
                
                // Check if CO2 is critical before setting translation
                const isCo2Critical = document.getElementById('card-co2').getAttribute('data-critical') === 'true';
                document.getElementById('t-co2-desc').textContent = isCo2Critical ? translations[currentLang].co2_desc_critical : translations[currentLang].co2_desc_stable;
                
                document.getElementById('t-chart-title').textContent = translations[currentLang].chart_title;
                document.getElementById('t-sensor').textContent = translations[currentLang].sensor;

                // Update theme button text
                const isLight = document.body.classList.contains('light-mode');
                themeText.textContent = isLight 
                    ? translations[currentLang].theme_dark 
                    : translations[currentLang].theme_light;
            });

            // ================= DATA UPDATER (For DB/MQTT) =================
            window.updateDashboardData = function(data) {
                if(data.airQuality !== undefined) document.getElementById('val-aq').textContent = data.airQuality;
                if(data.temperature !== undefined) document.getElementById('val-temp').textContent = data.temperature;
                if(data.humidity !== undefined) document.getElementById('val-hum').textContent = data.humidity;
                
                if(data.co2 !== undefined) {
                    document.getElementById('val-co2').textContent = data.co2;
                    const co2Card = document.getElementById('card-co2');
                    const co2Dot = co2Card.querySelector('.status-dot');
                    const co2Desc = document.getElementById('t-co2-desc');
                    const co2Icon = co2Card.querySelector('.card-icon');
                    
                    if (data.co2 < 800) {
                        co2Card.setAttribute('data-critical', 'false');
                        co2Dot.className = 'status-dot normal';
                        co2Desc.className = 'card-desc desc-stable';
                        co2Desc.textContent = translations[currentLang].co2_desc_stable;
                        co2Icon.classList.remove('critical-icon');
                    } else {
                        co2Card.setAttribute('data-critical', 'true');
                        co2Dot.className = 'status-dot critical';
                        co2Desc.className = 'card-desc desc-critical';
                        co2Desc.textContent = translations[currentLang].co2_desc_critical;
                        co2Icon.classList.add('critical-icon');
                    }
                }
            };
        });
    </script>
</body>
</html>
