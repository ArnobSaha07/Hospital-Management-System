<div class="d-flex align-items-center mt-2">
    {{ empty($row->user->phone) ? 'N/A' : $row->user->region_code.$row->user->phone  }}
</div>

