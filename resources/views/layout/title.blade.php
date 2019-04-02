<div id="title-div" class="row center-block">
    <div class="hidden-sm hidden-md hidden-lg">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h4><a id="simple-title-a" href="/"> {{ config('app.name') }} </a></h4>
        </div>
    </div>
    <div class="hidden-xs">
        <div class="row">
            <table id="complete-title-table" class="table-borderless">
                <tr>
                    <td><a href="/"><img class="logo" src="{{ URL::asset('images/kkis_logo.png') }}"></a></td>
                    <td><h3 id="complete-title-h3">{{ config('app.name') }}</h3></td>
                </tr>
            </table>
        </div>
    </div>
</div>
