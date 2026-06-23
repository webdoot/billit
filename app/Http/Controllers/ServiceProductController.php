<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceProductRequest;
use App\Http\Requests\UpdateServiceProductRequest;
use App\Models\ServiceProduct;
use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;
use App\Repositories\Contracts\ServiceProductRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceProductController extends Controller
{
    protected ServiceProductRepositoryInterface $productRepo;
    protected ServiceCategoryRepositoryInterface $categoryRepo;

    public function __construct(
        ServiceProductRepositoryInterface $productRepo,
        ServiceCategoryRepositoryInterface $categoryRepo
    ) {
        $this->productRepo = $productRepo;
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * Display a listing of products.
     */
    public function index(): View
    {
        $this->authorize('services.view');
        
        $products = ServiceProduct::with('category')->get();

        return view('service-products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $this->authorize('services.create');
        $categories = $this->categoryRepo->all()->where('status', 'Active');

        return view('service-products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreServiceProductRequest $request): RedirectResponse
    {
        $this->productRepo->create($request->validated());

        return redirect()->route('service-products.index')
            ->with('success', 'Service Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(ServiceProduct $serviceProduct): View
    {
        $this->authorize('services.edit');
        $categories = $this->categoryRepo->all()->where('status', 'Active');

        return view('service-products.edit', compact('serviceProduct', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateServiceProductRequest $request, ServiceProduct $serviceProduct): RedirectResponse
    {
        $this->productRepo->update($serviceProduct->id, $request->validated());

        return redirect()->route('service-products.index')
            ->with('success', 'Service Product updated successfully.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(ServiceProduct $serviceProduct): RedirectResponse
    {
        $this->authorize('services.delete');

        // Check if there are customer services using this product
        if ($serviceProduct->customerServices()->exists()) {
            return redirect()->route('service-products.index')
                ->with('error', 'Cannot delete product. There are active customer service agreements using this product.');
        }

        $this->productRepo->delete($serviceProduct->id);

        return redirect()->route('service-products.index')
            ->with('success', 'Service Product deleted successfully.');
    }
}
