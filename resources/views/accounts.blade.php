@extends("layout")
@section("title")
    Accounts
@endsection
@section("content")

<div class="container">
    <div class="row justify-content-center">
        <div class="col col-md-10 col-lg-8">
            <div class="card shadow-card border-0">
                <div class="card-header">
                    <h5 class="card-title mb-0">Accounts</h5>
                </div>
                <div class="card-body pb-2">
                    <div class="row justify-content-start">
                        <div class="col">
                            <button type="submit" class="btn btn-primary mr-2">Connect Digital Account</button>
                            <button type="button" class="btn btn-secondary">Create Cash Wallet</button>
                        </div>
                    </div>
                    <div class="row mt-5 text-secondary text-center account-mini-headers">
                        <div class="col-6 text-left">ACCOUNT NAME</div>
{{--                        <div class="col">ACCOUNT TYPE</div>--}}
                        <div class="col">CURRENT BALANCE</div>
                        <div class="col">TURN ON/OFF</div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="accordion" id="accordion-accounts">
                        @foreach($groups as $key => $group)
                            <div class="card">
                                <div class="card-header py-3 hover-disabled">
                                    <div class="row">
                                        @if($key == \App\Models\Account::TYPE_CASH)
                                            <div class="col text-uppercase">
                                                <i class="fas fa-wallet mr-3 category-icon text-secondary"></i>cash wallets
                                            </div>

                                        @elseif($key == \App\Models\Account::TYPE_CARD)
                                            <div class="col text-uppercase">
                                                <i class="fas fa-wallet mr-3 category-icon text-secondary"></i>cards
                                            </div>
                                        @endif
                                            <div class="col"></div>
                                            <div class="col d-flex justify-content-center"> ILS</div>
                                            <div class="col d-flex justify-content-center">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="bank-category-toggle" checked>
                                                    <label class="custom-control-label " for="bank-category-toggle"></label>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @foreach($group as $account)
                                            @php /** @var \App\Models\Account $account */ @endphp
                                            @include('accounts.account-manage-item', [
                                                                                "id" => $account->id,
                                                                                "balance" => $account->balance,
                                                                                "currency" => $account->currency,
                                                                                "initialAmount" => "0 to do",
                                                                                "accountType" => $account->type,
                                                                                "accountName" => $account->title
                                                                                ])
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section("scripts")
    <script>
        $(document).ready(function(){
            $("select").select2({
                theme : "bootstrap"
            });
        });

        $(".js-visibility-toggle").change(function(){
            $(this).closest(".input-group").find('.js-visibility-toggled').attr('readonly', function(_, attr){ return !attr}).focus();
            $(this).closest(".input-group-prepend").hide();
        })

        $(".js-visibility-toggled").blur(function(){
            $(this).closest(".input-group").find('.input-group-prepend').show();
            $(this).attr('readonly', function(_, attr){ return !attr});
        })
    </script>
@endsection


