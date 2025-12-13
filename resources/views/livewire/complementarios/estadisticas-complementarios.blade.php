<div wire:poll.120s="cargarDatos" wire:init="cargarDatos">
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 mb-2 text-primary"><i class="fas fa-users"></i></div>
                    <div class="h4 mb-0" id="total-aspirantes">
                        {{ number_format($totalAspirantes) }}
                    </div>
                    <small class="text-muted">Total Aspirantes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 mb-2 text-success"><i class="fas fa-user-check"></i></div>
                    <div class="h4 mb-0" id="aspirantes-aceptados">
                        {{ number_format($aspirantesAceptados) }}
                    </div>
                    <small class="text-muted">Aspirantes Aceptados</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 mb-2 text-warning"><i class="fas fa-user-clock"></i></div>
                    <div class="h4 mb-0" id="aspirantes-pendientes">
                        {{ number_format($aspirantesPendientes) }}
                    </div>
                    <small class="text-muted">Aspirantes Pendientes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <div class="h2 mb-2 text-info"><i class="fas fa-graduation-cap"></i></div>
                    <div class="h4 mb-0" id="programas-activos">
                        {{ number_format($programasActivos) }}
                    </div>
                    <small class="text-muted">Programas Activos</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                    <strong>Tendencia de Inscripciones</strong>
                    <small class="text-muted">
                        <i class="fas fa-sync-alt fa-spin" wire:loading></i>
                        <span wire:loading.remove>Actualización automática cada 2 minutos</span>
                    </small>
                </div>
                <div class="card-body" style="position: relative; height: 300px;">
                    <canvas id="inscripcionesChart" wire:ignore></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <strong>Distribución por Programas</strong>
                </div>
                <div class="card-body" style="position: relative; height: 300px;">
                    <canvas id="programasPieChart" wire:ignore></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Programas con Mayor Demanda</strong>
            <div>
                <button wire:click="refrescar" 
                        class="btn btn-outline-secondary btn-sm me-2"
                        title="Refrescar manualmente">
                    <i class="fas fa-sync-alt" wire:loading.class="fa-spin"></i> Refrescar
                </button>
                <a href="{{ route('complementarios.estadisticas.exportar-excel') }}"
                   class="btn btn-outline-primary btn-sm"
                   title="Exportar a Excel">
                    <i class="fas fa-file-excel me-1"></i>Exportar Excel
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Nombre del Programa</th>
                            <th>Total Aspirantes</th>
                            <th>Aceptados</th>
                            <th>Pendientes</th>
                            <th>Tasa de Aceptación</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-programas-demanda">
                        @forelse ($programasDemanda as $programa)
                            <tr>
                                <td>{{ $programa['programa'] }}</td>
                                <td>{{ $programa['total_aspirantes'] }}</td>
                                <td>{{ $programa['aceptados'] }}</td>
                                <td>{{ $programa['pendientes'] }}</td>
                                <td>{{ $programa['tasa_aceptacion'] }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hay datos disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Datos iniciales para JavaScript -->
    <script type="application/json" id="estadisticas-data" wire:ignore>
        @json([
            'tendencia_inscripciones' => $tendenciaInscripciones,
            'distribucion_programas' => $distribucionProgramas
        ])
    </script>

    <script wire:ignore>
        (function() {
            let inscripcionesChart, programasPieChart;
            let componentId = null;

            // Función para formatear datos de tendencia
            function formatearTendenciaInscripciones(tendencia) {
                const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                const labels = [];
                const data = [];

                // Crear array de los últimos 6 meses
                const ultimosMeses = [];
                for (let i = 5; i >= 0; i--) {
                    const fecha = new Date();
                    fecha.setMonth(fecha.getMonth() - i);
                    ultimosMeses.push({
                        year: fecha.getFullYear(),
                        month: fecha.getMonth() + 1
                    });
                }

                // Mapear datos existentes
                const datosPorMes = {};
                if (Array.isArray(tendencia)) {
                    tendencia.forEach(item => {
                        const key = `${item.year}-${item.month}`;
                        datosPorMes[key] = item.total || 0;
                    });
                }

                // Llenar con datos reales o cero
                ultimosMeses.forEach(({ year, month }) => {
                    const key = `${year}-${month}`;
                    labels.push(`${meses[month - 1]} ${year}`);
                    data.push(datosPorMes[key] || 0);
                });

                return { labels, data };
            }

            // Función para obtener datos del componente Livewire
            function obtenerDatosDelComponente() {
                if (!componentId) {
                    // Buscar el componente Livewire
                    const wireElement = document.querySelector('[wire\\:id]');
                    if (wireElement) {
                        componentId = wireElement.getAttribute('wire:id');
                    }
                }

                if (componentId && window.Livewire) {
                    const component = window.Livewire.find(componentId);
                    if (component) {
                        return {
                            tendencia_inscripciones: component.get('tendenciaInscripciones') || [],
                            distribucion_programas: component.get('distribucionProgramas') || []
                        };
                    }
                }

                // Fallback: usar datos iniciales desde el DOM
                const dataScript = document.getElementById('estadisticas-data');
                if (dataScript) {
                    return JSON.parse(dataScript.textContent);
                }

                return {
                    tendencia_inscripciones: @json($tendenciaInscripciones),
                    distribucion_programas: @json($distribucionProgramas)
                };
            }

            // Función para inicializar o actualizar gráficos
            function actualizarGraficos() {
                const estadisticas = obtenerDatosDelComponente();

                // Datos de tendencia de inscripciones
                const tendenciaData = formatearTendenciaInscripciones(estadisticas.tendencia_inscripciones || []);

                // Actualizar o crear gráfico de línea
                const ctxLine = document.getElementById('inscripcionesChart');
                if (ctxLine) {
                    if (inscripcionesChart) {
                        inscripcionesChart.data.labels = tendenciaData.labels;
                        inscripcionesChart.data.datasets[0].data = tendenciaData.data;
                        inscripcionesChart.update('none');
                    } else {
                        inscripcionesChart = new Chart(ctxLine.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: tendenciaData.labels,
                                datasets: [{
                                    label: 'Inscripciones',
                                    data: tendenciaData.data,
                                    borderColor: '#0d6efd',
                                    backgroundColor: 'rgba(13,110,253,0.1)',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#0d6efd'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    }
                }

                // Datos para gráfico de distribución
                const distribucionData = estadisticas.distribucion_programas || [];
                const labelsPie = distribucionData.map(item => item.programa || '');
                const dataPie = distribucionData.map(item => item.total || 0);

                // Actualizar o crear gráfico de pastel
                const ctxPie = document.getElementById('programasPieChart');
                if (ctxPie) {
                    if (programasPieChart) {
                        programasPieChart.data.labels = labelsPie;
                        programasPieChart.data.datasets[0].data = dataPie;
                        programasPieChart.update('none');
                    } else {
                        programasPieChart = new Chart(ctxPie.getContext('2d'), {
                            type: 'pie',
                            data: {
                                labels: labelsPie,
                                datasets: [{
                                    data: dataPie,
                                    backgroundColor: [
                                        '#0d6efd', '#dc3545', '#ffc107', '#20c997', '#6f42c1',
                                        '#fd7e14', '#e83e8c', '#20c997', '#6610f2', '#6c757d'
                                    ]
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            boxWidth: 12,
                                            font: {
                                                size: 10
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            }

            // Inicializar cuando Livewire esté listo
            document.addEventListener('livewire:init', () => {
                setTimeout(() => {
                    actualizarGraficos();
                }, 100);
            });

            // Actualizar cuando Livewire actualice el componente
            document.addEventListener('livewire:updated', () => {
                actualizarGraficos();
            });

            // Escuchar evento de Livewire
            document.addEventListener('livewire:init', () => {
                Livewire.on('estadisticas-actualizadas', () => {
                    setTimeout(actualizarGraficos, 100);
                });
            });

            // Inicializar gráficos al cargar la página (fallback)
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(actualizarGraficos, 500);
                });
            } else {
                setTimeout(actualizarGraficos, 500);
            }
        })();
    </script>
</div>

