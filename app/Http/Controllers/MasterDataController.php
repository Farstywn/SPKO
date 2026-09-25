<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Product;
use App\Models\WorkAllocation;
use App\Models\WorkCompletion;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    /**
     * Dashboard Ringkasan Sistem ERP.
     */
    public function dashboard()
    {
        $totalSpko = WorkAllocation::count();
        $totalNthko = WorkCompletion::count();
        $totalEmployee = Employee::count();
        $totalProduct = Product::count();

        $recentSpko = WorkAllocation::with(['employee', 'items.product'])
            ->orderBy('TransDate', 'desc')
            ->orderBy('ID', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalSpko',
            'totalNthko',
            'totalEmployee',
            'totalProduct',
            'recentSpko'
        ));
    }

    /**
     * Master Data Operator (Employee).
     */
    public function employees(Request $request)
    {
        $search = $request->query('search');

        $query = Employee::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('rank', 'like', "%{$search}%")
                  ->orWhere('Id_employee', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('nama')->paginate(15)->withQueryString();

        return view('master.employee', compact('employees', 'search'));
    }

    /**
     * Master Data Produk (Product / FG).
     */
    public function products(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');

        $query = Product::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('Id_product', 'like', "%{$search}%")
                  ->orWhere('serial_no', 'like', "%{$search}%");
            });
        }

        if (!empty($category)) {
            $query->where('sub_category', $category);
        }

        $categories = Product::select('sub_category')->distinct()->pluck('sub_category');
        $products = $query->orderBy('description')->paginate(15)->withQueryString();

        return view('master.product', compact('products', 'categories', 'search', 'category'));
    }

    /**
     * Transaksi Nota Terima Hasil Kerja Operator (NTHKO).
     */
    public function nthko(Request $request)
    {
        $search = $request->query('search');

        $query = WorkCompletion::with(['employee', 'items.product', 'workAllocation']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('WorkAllocation', 'like', "%{$search}%")
                  ->orWhere('ID', 'like', "%{$search}%")
                  ->orWhere('Process', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($eq) use ($search) {
                      $eq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $completions = $query->orderBy('TransDate', 'desc')
            ->orderBy('ID', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('nthko.index', compact('completions', 'search'));
    }
}
