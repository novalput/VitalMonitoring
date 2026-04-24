<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ICU Patient Monitoring Dashboard — Real-time tracking of Heart Rate, SpO2, Body Temperature, and Blood Pressure.">
    <title>Patient Vital Monitoring | ICU Central</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- MQTT.js -->
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-slate-900 dark:text-white min-h-screen p-8 leading-normal bg-slate-100 dark:bg-[#0B0F19]">

    <div class="max-w-[1400px] mx-auto">

        <!-- ── Controls: Theme & Language ─────────────────────── -->
        <div class="flex justify-start md:justify-end gap-4 mb-4 md:-mb-12 relative z-10 animate-[fadeInDown_0.8s_ease-out]">

            <button id="lang-toggle"
                class="bg-black/5 dark:bg-white/10 border border-black/5 dark:border-white/5 text-slate-900 dark:text-white px-3 py-2 rounded-full font-['Outfit'] font-semibold text-sm cursor-pointer backdrop-blur-sm flex items-center gap-2 hover:bg-black/10 dark:hover:bg-white/20"
                title="Switch language">
                <!-- Flag image of the CURRENT language -->
                <img id="lang-flag"
                    src="https://flagcdn.com/w40/gb.png"
                    alt="English"
                    class="w-6 h-auto rounded-sm shadow-sm">
                <span id="lang-label" class="text-xs font-bold tracking-wider">EN</span>
            </button>

            <button id="theme-toggle"
                class="bg-black/5 dark:bg-white/10 border border-black/5 dark:border-white/5 text-slate-900 dark:text-white px-4 py-2 rounded-full font-['Outfit'] font-semibold text-sm cursor-pointer backdrop-blur-sm flex items-center gap-2 hover:bg-black/10 dark:hover:bg-white/20"
                title="Toggle Theme">
                <i class="ph ph-moon text-xl" id="theme-icon"></i>
                <span id="theme-text">Dark</span>
            </button>
        </div>

        <!-- ── Header ──────────────────────────────────────────── -->
        <header class="mb-10 animate-[fadeInDown_0.8s_ease-out]">
            <h1 class="font-['Outfit'] text-4xl md:text-5xl font-bold tracking-tight bg-gradient-to-r from-red-600 to-blue-500 dark:from-white dark:to-red-300 bg-clip-text text-transparent mb-1"
                id="t-title">Patient Vital Monitoring</h1>
            <div class="subtitle text-sm font-semibold text-emerald-500 tracking-[1.5px] flex items-center gap-2 uppercase"
                id="t-subtitle">ICU UNIT 04 - CENTRAL SYSTEM ACTIVE</div>
        </header>

        <!-- ── Sensor Cards ────────────────────────────────────── -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12" id="cards-container">
            <!-- Cards will be generated dynamically by JavaScript -->
        </div><!-- /cards-grid -->

    </div><!-- /container -->

    <!-- ── Script ─────────────────────────────────────────────── -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── Mini Charts Setup ──────────────────────────────────
            const miniCharts = {};
            const miniChartData = {};

            // ── Theme Toggle ───────────────────────────────────────
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeIcon      = document.getElementById('theme-icon');
            const themeText      = document.getElementById('theme-text');

            themeToggleBtn.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark');
                const isDark = document.documentElement.classList.contains('dark');

                themeIcon.className  = isDark ? 'ph ph-moon text-xl' : 'ph ph-sun text-xl';
                themeText.textContent = isDark
                    ? translations[currentLang].theme_dark
                    : translations[currentLang].theme_light;
            });

            // ── Dynamic Sensor Configuration ───────────────────────
            const SENSOR_CONFIG = {
                'Sensor_Modbus_1_HeartRate': {
                    icon: 'ph-heartbeat', colorTheme: 'red', unit: 'BPM',
                    title: { en: 'Heart Rate', id: 'Detak Jantung' },
                    desc: { en: 'Normal Sinus', id: 'Sinus Normal' },
                    thresholds: { min: 60, max: 100 },
                    descCritical: { en: 'Abnormal Rate', id: 'Detak Abnormal' }
                },
                'Sensor_Modbus_1_BodyTemp': {
                    icon: 'ph-thermometer', colorTheme: 'orange', unit: '°C',
                    title: { en: 'Body Temp', id: 'Suhu Tubuh' },
                    desc: { en: 'Stable', id: 'Stabil' },
                    thresholds: { min: 36.0, max: 37.5 },
                    descCritical: { en: 'Temperature Alert', id: 'Peringatan Suhu' }
                },
                'Sensor_Modbus_1_SpO2': {
                    icon: 'ph-drop', colorTheme: 'cyan', unit: '%',
                    title: { en: 'SpO2', id: 'SpO2' },
                    desc: { en: 'Optimal Saturation', id: 'Saturasi Optimal' },
                    thresholds: { min: 95, max: 100 },
                    descCritical: { en: 'Low Saturation', id: 'Saturasi Rendah' }
                },
                'Sensor_Modbus_1_EKG': {
                    icon: 'ph-waveform', colorTheme: 'purple', unit: 'ms',
                    title: { en: 'EKG', id: 'EKG' },
                    desc: { en: 'Stable', id: 'Stabil' }
                },
                'Sensor_Modbus_1_CombinedBP': {
                    icon: 'ph-activity', colorTheme: 'blue', unit: 'mmHg',
                    title: { en: 'Blood Pressure', id: 'Tekanan Darah' },
                    desc: { en: 'SYS / DIA', id: 'SYS / DIA' }
                },
                'Sensor_Modbus_1_Elektrodermal': {
                    icon: 'ph-lightning', colorTheme: 'emerald', unit: 'µS',
                    title: { en: 'Electrodermal', id: 'Elektrodermal' },
                    desc: { en: 'Skin Conductance', id: 'Konduktansi Kulit' }
                }
            };

            const THEMES = {
                red: { text: 'text-red-500 dark:text-red-400', bg: 'bg-red-500/10' },
                orange: { text: 'text-orange-500 dark:text-orange-400', bg: 'bg-orange-500/10' },
                cyan: { text: 'text-cyan-500 dark:text-cyan-400', bg: 'bg-cyan-500/10' },
                blue: { text: 'text-blue-500 dark:text-blue-400', bg: 'bg-blue-500/10' },
                purple: { text: 'text-purple-500 dark:text-purple-400', bg: 'bg-purple-500/10' },
                pink: { text: 'text-pink-500 dark:text-pink-400', bg: 'bg-pink-500/10' },
                default: { text: 'text-emerald-500 dark:text-emerald-400', bg: 'bg-emerald-500/10' }
            };

            const cardsContainer = document.getElementById('cards-container');

            function renderCard(sensorKey, value) {
                let card = document.getElementById(`card-${sensorKey}`);
                const config = SENSOR_CONFIG[sensorKey] || {
                    icon: 'ph-pulse', colorTheme: 'default', unit: '',
                    title: { en: sensorKey.replace('Sensor_', ''), id: sensorKey.replace('Sensor_', '') },
                    desc: { en: 'Monitoring', id: 'Memantau' }
                };
                const theme = THEMES[config.colorTheme] || THEMES.default;

                if (!card) {
                    card = document.createElement('div');
                    card.id = `card-${sensorKey}`;
                    card.className = `bg-white/80 dark:bg-[rgba(22,33,62,0.6)] backdrop-blur-xl border border-black/5 dark:border-white/5 rounded-[1.25rem] p-6 flex flex-col shadow relative overflow-hidden opacity-0 animate-[fadeInUp_0.8s_ease-out_forwards] hover:-translate-y-[5px] hover:shadow-[0_10px_25px_rgba(59,130,246,0.15)] hover:bg-white dark:hover:bg-[rgba(30,58,138,0.4)] transition-[box-shadow,background-color,border-color,transform] duration-300 cursor-default`;
                    const childCount = cardsContainer.children.length;
                    card.style.animationDelay = `${0.1 + (childCount * 0.1)}s`;

                    card.innerHTML = `
                        <div class="flex justify-between items-start mb-2">
                            <div class="text-[2rem] leading-none ${theme.text} ${theme.bg} p-3 rounded-2xl">
                                <i class="ph ${config.icon}"></i>
                            </div>
                            <div class="status-dot w-3 h-3 rounded-full shadow-[0_0_10px_currentColor] bg-emerald-500 text-emerald-500 dark:bg-white dark:text-white" title="Normal"></div>
                        </div>
                        <div class="text-base text-slate-500 dark:text-slate-400 font-medium mb-1" id="title-${sensorKey}"></div>
                        <div class="font-['Outfit'] text-[2.25rem] font-bold mb-1 flex items-baseline gap-1">
                            <span id="val-${sensorKey}">${value !== undefined ? value : '--'}</span>
                            <span class="text-base text-slate-500 dark:text-slate-400 font-medium">${config.unit}</span>
                        </div>
                        <div class="text-sm font-medium text-emerald-500 mb-2" id="desc-${sensorKey}"></div>
                        ${sensorKey !== 'Sensor_Modbus_1_CombinedBP' ? `<div class="relative h-12 w-full mt-auto"><canvas id="mini-chart-${sensorKey}"></canvas></div>` : ''}
                    `;
                    cardsContainer.appendChild(card);

                    // Initialize Mini Chart
                    if (sensorKey !== 'Sensor_Modbus_1_CombinedBP') {
                        miniChartData[sensorKey] = { labels: [], data: [] };
                        const ctxCard = document.getElementById(`mini-chart-${sensorKey}`).getContext('2d');
                        
                        const isDark = document.documentElement.classList.contains('dark');
                        let hexColor = isDark ? '#10B981' : '#059669';
                        if (config.colorTheme === 'red') hexColor = '#EF4444';
                        if (config.colorTheme === 'orange') hexColor = '#F97316';
                        if (config.colorTheme === 'cyan') hexColor = '#06B6D4';
                        if (config.colorTheme === 'blue') hexColor = '#3B82F6';
                        if (config.colorTheme === 'purple') hexColor = '#A855F7';
                        if (config.colorTheme === 'pink') hexColor = '#EC4899';
                        
                        const gradient = ctxCard.createLinearGradient(0, 0, 0, 50);
                        gradient.addColorStop(0, hexColor + '40'); // 25% opacity
                        gradient.addColorStop(1, hexColor + '00'); // 0% opacity

                        miniCharts[sensorKey] = new Chart(ctxCard, {
                            type: 'line',
                            data: {
                                labels: miniChartData[sensorKey].labels,
                                datasets: [{
                                    data: miniChartData[sensorKey].data,
                                    borderColor: hexColor,
                                    backgroundColor: gradient,
                                    borderWidth: 2,
                                    pointRadius: 0,
                                    pointHoverRadius: 3,
                                    tension: 0.4,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                                scales: { x: { display: false }, y: { display: false } },
                                layout: { padding: 0 }
                            }
                        });
                    }
                } else if (value !== undefined) {
                    document.getElementById(`val-${sensorKey}`).textContent = value;
                }

                // Translations and Thresholds
                const tTitle = document.getElementById(`title-${sensorKey}`);
                const tDesc = document.getElementById(`desc-${sensorKey}`);
                const dot = card.querySelector('.status-dot');

                if (tTitle) tTitle.textContent = config.title[currentLang] || config.title.en;
                
                let currentDesc = config.desc[currentLang] || config.desc.en;
                let isCritical = false;

                if (value !== undefined && config.thresholds) {
                    const numVal = parseFloat(value);
                    if (!isNaN(numVal) && (numVal < config.thresholds.min || numVal > config.thresholds.max)) {
                        isCritical = true;
                        currentDesc = config.descCritical ? (config.descCritical[currentLang] || config.descCritical.en) : currentDesc;
                    }
                }

                if (tDesc) {
                    tDesc.textContent = currentDesc;
                    if (isCritical) {
                        tDesc.className = 'text-sm font-medium text-red-500';
                        dot.className = 'status-dot w-3 h-3 rounded-full shadow-[0_0_10px_currentColor] bg-red-500 text-red-500 animate-[flash_1.5s_infinite]';
                    } else {
                        tDesc.className = 'text-sm font-medium text-emerald-500';
                        dot.className = 'status-dot w-3 h-3 rounded-full shadow-[0_0_10px_currentColor] bg-emerald-500 text-emerald-500 dark:bg-white dark:text-white';
                    }
                }
            }

            // ── Translations ───────────────────────────────────────
            const translations = {
                en: {
                    theme_light:      'Light',
                    theme_dark:       'Dark',
                    title:            'Patient Vital Monitoring',
                    subtitle:         'ICU UNIT 04 - CENTRAL SYSTEM ACTIVE',
                    chart_title:      'Real-time Heart Rate Trend',
                    sensor:           'PATIENT ID: RD-00124',
                },
                id: {
                    theme_light:      'Terang',
                    theme_dark:       'Gelap',
                    title:            'Pemantauan Vital Pasien',
                    subtitle:         'UNIT ICU 04 - SISTEM PUSAT AKTIF',
                    chart_title:      'Tren Detak Jantung (Real-time)',
                    sensor:           'ID PASIEN: RD-00124',
                },
            };

            // Flags: country code of the CURRENT language
            // flagcdn.com serves flags by ISO 3166-1 alpha-2 code
            const langFlags  = { en: 'gb', id: 'id' };   // en → show 🇬🇧, id → show 🇮🇩
            const langAlts   = { en: 'English', id: 'Indonesian' };
            const langLabels = { en: 'EN', id: 'ID' };

            let currentLang = 'en';
            const langToggleBtn = document.getElementById('lang-toggle');
            const langFlag      = document.getElementById('lang-flag');  // <img>
            const langLabel     = document.getElementById('lang-label');

            // Apply all UI strings for `currentLang`
            function applyTranslations() {
                const t = translations[currentLang];
                document.getElementById('t-title').textContent       = t.title;
                document.getElementById('t-subtitle').textContent    = t.subtitle;

                const isDark = document.documentElement.classList.contains('dark');
                themeIcon.className = isDark ? 'ph ph-moon text-xl' : 'ph ph-sun text-xl';
                themeText.textContent = isDark ? t.theme_dark : t.theme_light;

                // Update flag image to show the target language
                langFlag.src          = `https://flagcdn.com/w40/${langFlags[currentLang]}.png`;
                langFlag.alt          = langAlts[currentLang];
                langLabel.textContent = langLabels[currentLang];

                // Update dynamic cards translations
                const activeCards = document.querySelectorAll('[id^="card-"]');
                activeCards.forEach(card => {
                    const sensorKey = card.id.replace('card-', '');
                    renderCard(sensorKey);
                });
            }

            // ── Auto-detect browser language on first load ─────────
            (function detectLang() {
                const browserLang = (navigator.language || navigator.userLanguage || 'en').toLowerCase();
                currentLang = browserLang.startsWith('id') ? 'id' : 'en';
                
                // Initialize default cards
                Object.keys(SENSOR_CONFIG).forEach(key => renderCard(key));

                applyTranslations();
            })();

            // ── Manual toggle ──────────────────────────────────────
            langToggleBtn.addEventListener('click', () => {
                currentLang = currentLang === 'en' ? 'id' : 'en';
                applyTranslations();
            });

            let currentSystolic = '--';
            let currentDiastolic = '--';

            // ── Data Updater (for DB / MQTT hook-in) ──────────────
            window.updateDashboardData = function (data) {
                let shouldUpdateBP = false;

                for (let [key, value] of Object.entries(data)) {
                    // Sesuaikan key (misal 'HeartRate' menjadi 'Sensor_HeartRate')
                    let sensorKey = key.startsWith('Sensor_') ? key : 'Sensor_' + key;
                    
                    // Format khusus untuk Suhu jika perlu (misal 302 -> 30.2)
                    if (sensorKey === 'Sensor_Modbus_1_BodyTemp' && value > 100) {
                        value = (value / 10).toFixed(1);
                    }

                    // Tangani Blood Pressure agar digabung
                    if (sensorKey === 'Sensor_Modbus_1_BloodPreassure') {
                        currentDiastolic = value;
                        shouldUpdateBP = true;
                    } else if (sensorKey === 'Sensor_Modbus_1_BloodPreassure2') {
                        currentSystolic = value;
                        shouldUpdateBP = true;
                    } else if (SENSOR_CONFIG[sensorKey]) {
                        // Update kartu lainnya
                        renderCard(sensorKey, value);

                        // Update mini chart
                        if (miniCharts[sensorKey] && value !== undefined) {
                            const numVal = parseFloat(value);
                            if (!isNaN(numVal)) {
                                miniChartData[sensorKey].labels.push('');
                                miniChartData[sensorKey].data.push(numVal);
                                if (miniChartData[sensorKey].labels.length > 20) {
                                    miniChartData[sensorKey].labels.shift();
                                    miniChartData[sensorKey].data.shift();
                                }
                                miniCharts[sensorKey].update('none');
                            }
                        }
                    }
                }

                // Render Blood Pressure gabungan
                if (shouldUpdateBP) {
                    renderCard('Sensor_Modbus_1_CombinedBP', `${currentSystolic} / ${currentDiastolic}`);
                }
            };

            // ── MQTT Connection to broker.hivemq.com ──────────────
            const brokerUrl = 'wss://broker.hivemq.com:8884/mqtt';
            const topic = 'data/HMIRSPAL/Sensor/1234';
            const clientId = 'dashboard_' + Math.random().toString(16).substr(2, 8);

            console.log('Connecting to MQTT Broker:', brokerUrl);
            const client = mqtt.connect(brokerUrl, {
                clientId: clientId,
                clean: true,
                connectTimeout: 4000,
                reconnectPeriod: 1000,
            });

            client.on('connect', function () {
                console.log('Connected to MQTT broker successfully!');
                client.subscribe(topic, function (err) {
                    if (!err) {
                        console.log('Subscribed to topic:', topic);
                    } else {
                        console.error('Subscription error:', err);
                    }
                });
            });

            client.on('message', function (t, message) {
                try {
                    const payload = message.toString();
                    
                    // Attempt to parse as JSON first
                    try {
                        const data = JSON.parse(payload);
                        window.updateDashboardData(data);
                    } catch (e) {
                        // Not JSON, assume it's a raw value from a wildcard topic (e.g. data/HMIRSPAL/Sensor/Sensor_HeartRate)
                        const topicParts = t.split('/');
                        let sensorKey = topicParts[topicParts.length - 1];
                        
                        // Fallback: If the topic just says 'HeartRate' without 'Sensor_', we can prefix it
                        if (!sensorKey.startsWith('Sensor_')) {
                            sensorKey = 'Sensor_' + sensorKey;
                        }
                        
                        const data = {};
                        data[sensorKey] = payload;
                        window.updateDashboardData(data);
                    }
                } catch (e) {
                    console.error('Error handling MQTT message:', e);
                }
            });

        });
    </script>
</body>
</html>
