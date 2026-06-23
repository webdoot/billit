<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceCategoryRequest;
use App\Http\Requests\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;
use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceCategoryController extends Controller
{
    protected ServiceCategoryRepositoryInterface $categoryRepo;

    public function __construct(ServiceCategoryRepositoryInterface $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * Display a listing of categories.
     */
    public function index(): View
    {
        $this->authorize('services.view');
        $categories = $this->categoryRepo->all();

        return view('service-categories.index', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreServiceCategoryRequest $request): RedirectResponse
    {
        $this->categoryRepo->create($request->validated());

        return redirect()->route('service-categories.index')
            ->with('success', 'Service Category created successfully.');
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $serviceCategory): RedirectResponse
    {
        $this->categoryRepo->update($serviceCategory->id, $request->validated());

        return redirect()->route('service-categories.index')
            ->with('success', 'Service Category updated successfully.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(ServiceCategory $serviceCategory): RedirectResponse
    {
        $this->authorize('services.delete');

        // Check if there are any products attached to this category
        if ($serviceCategory->serviceProducts()->exists()) {
            return redirect()->route('service-categories.index')
                ->with('error', 'Cannot delete category. There are products linked to this category.');
        }

        $this->categoryRepo->delete($serviceCategory->id);

        return redirect()->route('service-categories.index')
            ->with('success', 'Service Category deleted successfully.');
    }
}
