<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Models\Category;
use Models\PropertyType;
use Models\Feature;

class CategoryController
{
    public function categories(): void
    {
        Response::success(Category::all());
    }

    public function propertyTypes(): void
    {
        $catId = (int) Request::get('category_id');
        Response::success(PropertyType::all($catId ?: null));
    }

    public function features(): void
    {
        Response::success(Feature::all());
    }
}