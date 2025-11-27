<?php

declare(strict_types=1);

namespace App\Livewire\Inventario;

use App\Repositories\BD\DashboardRepository;
use Livewire\Component;

class DashboardInventario extends Component
{
    public int $totalProductos = 0;
    public int $productosConsumibles = 0;
    public int $productosNoConsumibles = 0;
    public int $productosPorVencer = 0;
    public int $productosStockBajo = 0;
    public int $totalCategorias = 0;
    public array $productosMasSolicitados = [];
    public array $productosPorCategoria = [];
    public array $productosRecientes = [];

    protected DashboardRepository $dashboardRepository;

    public function boot(DashboardRepository $dashboardRepository): void
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function mount(): void
    {
        $this->cargarDatos();
    }

    /**
     * Carga todos los datos del dashboard
     */
    public function cargarDatos(): void
    {
        $this->totalProductos = $this->dashboardRepository->obtenerTotalProductos();
        $this->productosConsumibles = $this->dashboardRepository->obtenerProductosConsumibles();
        $this->productosNoConsumibles = $this->dashboardRepository->obtenerProductosNoConsumibles();
        $this->productosPorVencer = $this->dashboardRepository->obtenerProductosPorVencer();
        $this->productosStockBajo = $this->dashboardRepository->obtenerProductosStockBajo();
        $this->totalCategorias = $this->dashboardRepository->obtenerTotalCategorias();
        $this->productosMasSolicitados = $this->dashboardRepository->obtenerProductosMasSolicitados(5);
        $this->productosPorCategoria = $this->dashboardRepository->obtenerProductosPorCategoria();
        $this->productosRecientes = $this->dashboardRepository->obtenerProductosRecientes(5);
    }

    /**
     * Refrescar manualmente los datos
     */
    public function refrescar(): void
    {
        $this->cargarDatos();
        $this->dispatch('datos-actualizados');
    }

    public function render()
    {
        return view('livewire.inventario.dashboard-inventario');
    }
}

