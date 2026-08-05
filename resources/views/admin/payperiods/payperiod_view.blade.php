@extends('layouts.master') @section('content')
<style>
.table.table-striped tbody tr:nth-child(odd) {
	background: #fff;
}
</style>
<div class="content-wrapper">
	@if(session('success'))
	<div class="alert alert-success">
		<h4>{{ session('success') }}</h4>
	</div>
	@endif
	@if(session('error'))
	<div class="alert alert-danger">
		<h4>{{ session('error') }}</h4>
	</div>
	@endif
  	<div class="row"></div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Payperiods</h4>
                    <div class="d-flex justify-content-between align-items-center mb-3 w-100">
                        <a href="{{ route('payperiods.create') }}" class="btn btn-primary btn-lg">
                            <i class="fa fa-plus-circle"></i> Add Payperiod
                        </a>
                        <form method="POST" action="{{ url('/payperiods/astore') }}" class="mb-0">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fa fa-plus-circle"></i> Auto Add Payperiod
                            </button>
                        </form>
                    </div>
                    <table id="sortable-table-1" class="table dataTable table-striped table-responsive">
                        <thead>
                            <tr>
                                <th class="sortStyle unsortStyle"> Sr. No<i class="mdi mdi-chevron-down"></i> </th>
                                <th class="sortStyle unsortStyle">Payperiod<i class="mdi mdi-chevron-down"></i> </th>
                                <th class="sortStyle unsortStyle">Company<i class="mdi mdi-chevron-down"></i> </th>
                                <th class="sortStyle unsortStyle">Start Date<i class="mdi mdi-chevron-down"></i> </th>
                                <th class="sortStyle unsortStyle">End Date<i class="mdi mdi-chevron-down"></i> </th>
                                <th class="sortStyle unsortStyle">Pay Date<i class="mdi mdi-chevron-down"></i> </th>
                                <th class="sortStyle unsortStyle">Reports<i class="mdi mdi-chevron-down"></i> </th>
                                <th class="sortStyle unsortStyle">Actions<i class="mdi mdi-chevron-down"></i> </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 1; ?> 
							@if($data->count() != 0) 
							@foreach ($data as $datas) 
							<?php $TodayDate = new DateTime(); $bet_dates = explode('-',$datas->payperiod_value); 
							if(isset($bet_dates)) {
                                $from_date = $bet_dates[0]; $to_date = $bet_dates[1]; 
							} $xto_date = explode('_',$to_date); $xto_date = implode('-',$xto_date); $xfrom_date = explode('_',$from_date); $xfrom_date =
                            implode('-',$xfrom_date); $xtto_date = new DateTime($xto_date); $xtfrom_date = new
                            DateTime($xfrom_date); ?> <?php if ( $TodayDate->format('y-m-d') >=
                            $xtfrom_date->format('y-m-d') && $TodayDate->format('y-m-d') <=
                            $xtto_date->format('y-m-d')){ ?>
                            <tr style="background: #0aab52">
                                <td style="color: #fff">
                                    <p
                                        style="
                                            color: #fff;
                                            font-size: 15px;
                                            text-transform: uppercase;
                                            font-weight: bold;
                                        "
                                    >
                                        CP
                                    </p>
                                    <p style="color: #fff; font-size: 15px"><?php echo $count; ?></p>
                                </td>
                                <td style="color: #fff">{{ $datas->payperiod }}</td>
                                <td style="color: #fff">{{ $datas->companies->company }}</td>
                                <td>
                                    <b style="font-size: 18px; color: #fff"
                                        >{{ date('d-M-Y', strtotime($xfrom_date)) }}</b
                                    >
                                </td>
                                <td>
                                    <b style="font-size: 18px; color: #fff">{{ date('d-M-Y',strtotime($xto_date)) }}</b>
                                </td>
                                <td>
                                    <b style="font-size: 18px; color: #cad42a"
                                        >{{ date('M d, Y', strtotime('+5 days', strtotime($xto_date))) }}</b
                                    >
                                </td>
                                <td>
                                    <a
                                        href="https://fitssheets.com/user/payroll/search/csvpostdata/<?php echo $datas->payperiod_value; ?>/<?php echo $datas->companies_id; ?>"
                                        id="ecsv"
                                        class="btn btn-success"
                                        >Export to CSV</a
                                    >
                                    <a
                                        href="{{ url('/userss/payroll-file/') }}/<?php echo $datas->payperiod_value; ?>/<?php echo $datas->companies_id; ?>"
                                        id="ecsv"
                                        class="btn btn-success"
                                        >Export Payroll</a
                                    >
                                </td>
                                <td style="color: #fff">
                                    <a href="{{ route('payperiods.edit', $datas->id) }}" title="Edit"
                                        ><i class="fa fa-pencil"></i
                                    ></a>
                                    <a
                                        style="margin-left: 5px"
                                        data-baseURL="{{ url('/') }}"
                                        data-ID="{{ $datas->id  }}"
                                        class="delete_payperiod"
                                        title="Delete"
                                        ><i class="fa fa-trash-o"></i
                                    ></a>
                                </td>
                            </tr>
                            <?php } else{ ?>
                            <tr>
                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}.</td>
                                <td>{{ $datas->payperiod }}</td>
                                <td>{{ $datas->companies->company }}</td>
                                <td>
                                    <b style="font-size: 18px; color: #f53838"
                                        >{{ date('d-M-Y', strtotime($xfrom_date)) }}</b
                                    >
                                </td>
                                <td>
                                    <b style="font-size: 18px; color: #f53838"
                                        >{{ date('d-M-Y',strtotime($xto_date)) }}</b
                                    >
                                </td>
                                <td>
                                    <b style="font-size: 18px; color: #0aab52"
                                        >{{ date('M d, Y', strtotime('+5 days', strtotime($xto_date))) }}</b
                                    >
                                </td>
                                <td>
                                    <a
                                        href="https://fitssheets.com//user/payroll/search/csvpostdata/<?php echo $datas->payperiod_value; ?>/<?php echo $datas->companies_id; ?>"
                                        id="ecsv"
                                        class="btn btn-success"
                                        >Export to CSV</a
                                    >
                                    <a
                                        href="{{ url('/userss/payroll-file/') }}/<?php echo $datas->payperiod_value; ?>/<?php echo $datas->companies_id; ?>"
                                        id="ecsv"
                                        class="btn btn-success"
                                        >Export Payroll</a
                                    >
                                </td>
                                <td>
                                    <a href="{{ route('payperiods.edit', $datas->id) }}" title="Edit"
                                        ><i class="fa fa-pencil"></i>
									</a>
									<a style="margin-left:10px;" class="delete_payperiod_record" data-payperiod_id ="{{ $datas->id }}" title="Delete">
										<i class="fa fa-trash-o"></i>
									</a>
                                    {{-- <a
                                        style="margin-left: 5px"
                                        data-baseURL="{{ url('/') }}"
                                        data-ID="{{ $datas->id  }}"
                                        class="delete_payperiod"
                                        title="Delete"
                                        ><i class="fa fa-trash-o"></i
                                    ></a> --}}
                                </td>
                            </tr>
                            <?php } ?> <?php $count++; ?> 
							@endforeach 
							@else
                            <tr>
								<td colspan="16" class="no-data">
								Sorry, No data found!
								</td>
							</tr>
                            @endif
                        </tbody>
                    </table>
					{{ $data->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
    <div class="loading"></div>
</div>
@endsection
