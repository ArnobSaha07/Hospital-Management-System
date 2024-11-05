<div>
    @if (Request::is('dashboard'))
        <div class="dashboard-skeleton">
            <div class="card">
                <div class="card-content">
                    <div class="d-flex justify-content-between">
                        <div class="row">
                            <div class="col-xxl-3 col-xl-4 col-sm-6 ">
                                <div class="widget pulsate rounded-1"></div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div class="widget-2 pulsate rounded-1"></div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div class="widget-2 pulsate rounded-1"></div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div class="widget-2 pulsate rounded-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="d-flex justify-content-between">
                        <div class="row">
                            <div class="col-xxl-3 col-xl-4 col-sm-6 ">
                                <div class="widget pulsate rounded-1"></div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div class="widget-2 pulsate rounded-1"></div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div class="widget-2 pulsate rounded-1"></div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div class="widget-2 pulsate rounded-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="card-content my-5">
                            <div class="row">
                                <div class="col-12 mb-5">
                                    <div class="column-box pulsate rounded-1"> </div>
                                </div>
                                @for ($i = 1; $i <= 6; $i++)
                                    <div class="col-4 mb-5">
                                        <div class="column-box pulsate rounded-1"> </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card-content my-5">
                            <div class="row">
                                <div class="col-12 mb-5">
                                    <div class="column-box pulsate rounded-1"> </div>
                                </div>
                                @for ($i = 1; $i <= 6; $i++)
                                    <div class="col-6 mb-5">
                                        <div class="column-box pulsate rounded-1"> </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (Request::is('super-admin/dashboard'))
        <div class="admin-dashboard-skeleton">
            <div class="card">
                <div class="card-content">
                    <div class="d-flex justify-content-between">
                        <div class="row">
                            <div class="col-xxl-3 col-xl-4 col-sm-6 ">
                                <div class="widget pulsate rounded-1"></div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div class="widget-2 pulsate rounded-1"></div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div class="widget-2 pulsate rounded-1"></div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div class="widget-2 pulsate rounded-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="d-flex justify-content-between">
                        <div class="search-box pulsate rounded-1"> </div>
                        <div class="d-flex">
                            <div class="filter-box pulsate rounded-1"> </div>
                            <div class="date-box pulsate rounded-1"> </div>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="row">
                        <div class="col-12">
                            <div class="chart pulsate rounded-1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (Request::is('patient/dashboard'))
        <div class="admin-dashboard-skeleton">
            <div class="card">
                <div class="card-content">
                    <div class="d-flex justify-content-between">
                        <div class="row">
                            <div class="col-xxl-3 col-xl-4 col-sm-6 ">
                                <div class="widget pulsate rounded-1"></div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div class="widget-2 pulsate rounded-1"></div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div class="widget-2 pulsate rounded-1"></div>
                            </div>
                            <div class="col-xxl-3 col-xl-4 col-sm-6">
                                <div class="widget-2 pulsate rounded-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <div class="d-flex justify-content-between">
                        <div class="search-box pulsate rounded-1"> </div>
                    </div>
                </div>
                <div class="card-content my-5">
                    <div class="table pulsate rounded-1"> </div>
                    <div class="row">
                        @for ($i = 1; $i <= 16; $i++)
                            <div class="col-3 mb-5">
                                <div class="column-box pulsate rounded-1"> </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
