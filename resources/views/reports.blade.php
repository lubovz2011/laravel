@extends("layout")
@section("title")
    Dashboard
@endsection
@section("content")

    <div class="container-fluid dynamic-height">
        <div class="row">
            <div class="col-3 bg-dark filters-form">
                @include('side-bar.filters-form')
            </div>
            <div class="col-9">
                <div class="card shadow-card border-0" style="min-height: 80vh;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Dashboard</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <div class="row py-1">
                                        <div class="col">
                                            <div class="btn-group btn-group-toggle d-flex justify-content-center" data-toggle="buttons">
                                                <a href="{{$links['all']}}" class="btn @if(request()->input('filter-types')) btn-secondary @else btn-primary @endif border-0 m-0">
                                                    All Types
                                                </a>
                                                <a href="{{$links['expense']}}" class="btn @if(request()->input('filter-types') == 'expense') btn-danger @else btn-secondary @endif border-white border-top-0 border-bottom-0 m-0">
                                                    Expense
                                                </a>
                                                <a href="{{$links['income']}}" class="btn @if(request()->input('filter-types') == 'income') btn-success @else btn-secondary @endif border-0 m-0">
                                                    Income
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col d-flex justify-content-end align-items-center">
                                            @if(isset($links['prev'], $links['next']))
                                                {{\Carbon\Carbon::parse(request()->input('filter-times'))->format('F, Y')}}
                                                <div class="btn-group btn-group-sm ml-2" role="group">
                                                    <a href="{{$links['prev']}}" class="btn btn-secondary border-white border-right-0">
                                                        <i class="mx-1 fas fa-chevron-left"></i>
                                                    </a>
                                                    <a href="{{$links['next']}}" class="btn btn-secondary border-white">
                                                        <i class="mx-1 fas fa-chevron-right"></i>
                                                    </a>
                                                </div>
                                            @else
                                                {{\App\Classes\Utils\DataSets::getDateOptions()[request()->input('filter-times')]}}
                                            @endif
                                        </div>
                                    </div>
                                </li>
                                @if($transactions->count())
                                <li class="list-group-item">
                                    <div class="row justify-content-center">
                                        <div class="col-12 d-flex justify-content-center align-items-center mb-4">
                                            <span class="js-total-income"></span>
                                            <span class="js-total-expense"></span>
                                        </div>
                                        <div class="col-5 my-2">
                                            <canvas id="myChart" width="400" height="400"></canvas>
                                        </div>

                                    </div>
                                    <div class="row mt-5 text-secondary text-center account-mini-headers">
                                        <div class="col-5 text-left">CATEGORY NAME</div>
                                        <div class="col d-flex justify-content-center">
                                            <div class="col">TRANSACTIONS</div>
                                            <div class="col d-flex justify-content-end mr-3">AMOUNT</div>
                                            <div class="col d-flex justify-content-end mr-3">PERCENT</div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group list-group-flush">

                                    <div class="accordion" id="accordion-categories">
                                        @php /** @var \App\Models\Category[] $filteredCategories*/ @endphp
                                        @foreach($filteredCategories->where('parent_id', '=', null) as $category)
                                            <div class="card">
                                                <div class="card-header js-parent-category row py-3" id="category-{{$category->id}}" data-toggle="collapse" data-target="#sub-category-{{$category->id}}">
                                                    <div class="col-5">
                                                        <i class="{{$category->icon}} mr-2 category-icon text-secondary"></i> <span class="js-category-name">{{$category->name}}</span>
                                                    </div>
                                                    <div class="col d-flex justify-content-center">
                                                        <div class="col">
                                                            <div class="text-center">
                                                                {{$category->getTransactionsCountForReport($transactions, $filteredCategories)}}
                                                            </div>
                                                        </div>
                                                        <div class="col d-flex justify-content-end">
                                                            <div class="mr-3 js-amount">
                                                                {{\App\Helpers\Helpers::NumberFormat($category->getAmountForReport($transactions, $filteredCategories))}}
                                                            </div>
                                                            <div class="text-secondary js-user-currency">{{$mainCurrency}}</div>
                                                        </div>
                                                        <div class="col d-flex justify-content-end">
                                                            <div class="mr-3 text-center @if($category->getAmountForReport($transactions, $filteredCategories) >= 0) text-success @else text-danger @endif font-weight-bold">
                                                                {{\App\Helpers\Helpers::NumberFormat($category->getPercentForReport($transactions, $filteredCategories, $totalIncome, $totalExpense))}}%
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($filteredCategories->where('parent_id', '=', $category->id)->count())
                                                    <div id="sub-category-{{$category->id}}" class="collapse" aria-labelledby="category-bills" data-parent="#accordion-categories">
                                                        <div class="card-body p-0">
                                                            <ul class="list-group list-group-flush">
                                                                @foreach($filteredCategories->where('parent_id', '=', $category->id) as $subCategory)
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-1">
                                                                        <div class="col-5">
                                                                            <i class="{{$subCategory->icon}} invisible mr-2 category-icon text-secondary"></i> {{$subCategory->name}}
                                                                        </div>
                                                                        <div class="col d-flex justify-content-center">
                                                                            <div class="col">
                                                                                <div class="text-center">{{$subCategory->getTransactionsCountForReport($transactions, $filteredCategories)}}</div>
                                                                            </div>
                                                                            <div class="col d-flex justify-content-end">
                                                                                <div class="mr-3">
                                                                                    {{\App\Helpers\Helpers::NumberFormat($subCategory->getAmountForReport($transactions, $filteredCategories))}}
                                                                                </div>
                                                                                <div class="text-secondary">{{$mainCurrency}}</div>
                                                                            </div>
                                                                            <div class="col d-flex justify-content-end">
                                                                                @if(request()->has('filter-types'))
                                                                                    <div class="mr-3 text-center @if($subCategory->getAmountForReport($transactions, $filteredCategories) >= 0) text-success @else text-danger @endif">
                                                                                        {{\App\Helpers\Helpers::NumberFormat($category->getChildPercentForReport($transactions, $filteredCategories, $subCategory))}}%
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </li>
                                @else
                                    <li class="list-group-item py-5">
                                        <div class="row justify-content-center align-content-center my-5">
                                            <div class="col text-center">
                                                You don't have any transactions yet.
                                            </div>
                                        </div>
                                        <div class="row justify-content-center align-content-center mt-5">
                                            <div class="col text-right">
                                                <a href="{{route('transactions')}}" class="btn btn-primary">Add transaction</a>
                                            </div>
                                            <div class="col text-left">
                                                <a href="{{route('accounts')}}" class="btn btn-primary">Add account</a>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                        </ul>
                    </div>
                    {{--<div class="card-body p-0">
                        <div class="accordion" id="accordion-accounts">
                            @foreach($transactionsByDate as $date => $transactions)
                                @include('transactions.transactions-manage-date', ["date" => $date, "transactions" => $transactions])
                            @endforeach
                        </div>
                    </div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section("scripts")
    <script>

        function number_format(number, decimals, dec_point, thousands_point) {

            if (number == null || !isFinite(number)) {
                throw new TypeError("number is not valid");
            }

            if (!decimals) {
                var len = number.toString().split('.').length;
                decimals = len > 1 ? len : 0;
            }

            if (!dec_point) {
                dec_point = '.';
            }

            if (!thousands_point) {
                thousands_point = ',';
            }

            number = parseFloat(number).toFixed(decimals);

            number = number.replace(".", dec_point);

            var splitNum = number.split(dec_point);
            splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
            number = splitNum.join(dec_point);

            return number;
        }

        $(document).ready(function(){
            $("select").select2({
                theme : "bootstrap"
            });

            $('select[name="filter-times"]').select2({
                "placeholder" : "Select time",
                theme : "bootstrap"
            });

            $('select[name="filter-types"]').select2({
                "placeholder" : "Select type",
                theme : "bootstrap"
            });

            $('select[name="filter-accounts[]"]').select2({
                "placeholder" : "Select account",
                theme : "bootstrap"
            });

            $('select[name="filter-categories[]"]').select2({
                "placeholder" : "Select category",
                theme : "bootstrap"
            });

            let data = {
                "amounts" : [],
                "labels"  : [],
                "backgroundColor"  : [],
                "borderColor"      : [],
                "totalIncome"      : 0,
                "totalExpense"     : 0

            };
            $('.js-parent-category').each(function(){
                let amount = +$('.js-amount', $(this)).text().trim().replace(',', '');
                data.amounts.push(amount);
                data.labels.push($('.js-category-name', $(this)).text());
                if(amount >= 0){
                    data.backgroundColor.push('rgba(40, 167, 69, 0.5)');
                    data.borderColor.push('rgba(255, 255, 255, 1)');
                    data.totalIncome += amount;
                }
                else{
                    data.backgroundColor.push('rgba(220, 53, 69, 0.5)');
                    data.borderColor.push('rgba(255, 255, 255, 1)');
                    data.totalExpense += amount;
                }
            });

            if(data.totalIncome)
                $('.js-total-income').html('Income :   <span class="text-success font-weight-bold">'
                                            + number_format(data.totalIncome, 2)
                                            + " "
                                            + '</span>'
                                            + $('.js-user-currency:first').text());
            else
                $('.js-total-income').remove()

            if(data.totalExpense)
                $('.js-total-expense').html('Expense :   <span class="text-danger font-weight-bold">'
                                            + number_format(data.totalExpense, 2)
                                            + " "
                                            + '</span>'
                                            + $('.js-user-currency:first').text());
            else
                $('.js-total-expense').remove()

            if(data.totalIncome && data.totalExpense)
                $('.js-total-income').addClass('mr-5')

            var ctx = document.getElementById('myChart');
            var myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: '# of Votes',
                        data: data.amounts,
                        backgroundColor: data.backgroundColor,
                        borderColor: data.borderColor,
                        borderWidth: 1
                    }],
                    responsive: true
                },

                options: {
                    legend: {
                        position: 'bottom'
                    }
                    /*scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }*/
                }
            });

        });
    </script>
@endsection
