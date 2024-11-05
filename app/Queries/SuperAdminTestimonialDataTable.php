<?php

namespace App\Queries;

use App\Models\AdminTestimonial;
use App\Models\Testimonial;

/**
 * Class SuperAdminTestimonialDataTable
 */
class SuperAdminTestimonialDataTable
{
    public function get(): Testimonial
    {
        /** @var testimonial $query */
        $query = AdminTestimonial::query()->select('admin_testimonials.*');

        return $query;
    }
}
