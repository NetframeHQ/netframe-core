<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>{{trans('accountent.bill.title')}} {{$bill->number}}</title>
    <style>
        .clearfix:after {
  content: "";
  display: table;
  clear: both;
}

a {
  color: #5D6975;
  text-decoration: underline;
}

body {
  position: relative;
  width: 20cm;  
  height: 28cm; 
  margin: 0 auto; 
  color: #001028;
  background: #FFFFFF; 
  font-family: Arial, sans-serif; 
  font-size: 12px; 
  font-family: Arial;
}

header {
  padding: 10px 0;
  margin-bottom: 30px;
  font-size: 14px;
}

#logo {
  text-align: center;
  margin-bottom: 10px;
}

#logo img {
  width: 150px;
}

h1 {
  border-top: 1px solid  #5D6975;
  border-bottom: 1px solid  #5D6975;
  color: #5D6975;
  font-size: 2.0em;
  line-height: 1.4em;
  font-weight: normal;
  text-align: center;
  margin: 0 0 20px 0;
}

#project {
  float: left;
  text-align: right;
  margin-right: 14%;
}

#project span {
  color: #5D6975;
  text-align: right;
  width: 52px;
  margin-right: 10px;
  display: inline-block;
  font-size: 0.8em;
}

#company {
  float: left;
  text-align: left;
}

#project div,
#company div {
  white-space: nowrap;        
}

table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 20px;
}

table tr:nth-child(2n-1) td {
  background: #F5F5F5;
}

table th,
table td {
  text-align: center;
}

table th {
  padding: 5px 20px;
  color: #5D6975;
  border-bottom: 1px solid #C1CED9;
  white-space: nowrap;        
  font-weight: normal;
}

table .service,
table .desc {
  text-align: left;
}

table td {
  padding: 20px;
  text-align: right;
}

table td.service,
table td.desc {
  vertical-align: top;
}

table td.unit,
table td.qty,
table td.total {
  font-size: 1.2em;
}

table td.grand {
  border-top: 1px solid #5D6975;;
}

#notices .notice {
  color: #5D6975;
  font-size: 1.2em;
}

footer {
  color: #5D6975;
  width: 100%;
  height: 30px;
  position: absolute;
  bottom: 0;
  border-top: 1px solid #C1CED9;
  padding: 8px 0;
  text-align: center;
}
    </style>
  </head>
  <body>
    <header class="clearfix">
      <div id="logo">
        @if(isset($pdf))
            <img src="assets/img/widget-logo.png" />
        @else
            <img src="{{asset('assets/img/widget-logo.png')}}" />
        @endif
      </div>
      <h1>{{trans('accountent.bill.title')}} {{$bill->number}}</h1>
      <div id="company" class="clearfix">
        <div>{{trans('accountent.billing.sender.name')}}</div>
        <div>{{trans('accountent.billing.sender.address')}},<br /> {{trans('accountent.billing.sender.codepostal')}}, {{trans('accountent.billing.sender.city')}}</div>
        <div>{{trans('accountent.billing.sender.tel')}}</div>
      </div>
      <div id="project">
        <div>&nbsp;</div>
        <div>{{$infos['designation']}}</div>
        <div>{{$infos['address']}},</div>
        <div>{{$infos['codepostal']}}, {{$infos['city']}}</div>
        <div>22/05/2018</div>
      </div>
    </header>
    <main>
      <table>
        <thead>
          <tr>
            <th class="desc">{{strtoupper(trans('accountent.billing.designation'))}}</th>
            <th>{{strtoupper(trans('accountent.billing.price'))}}</th>
            <th>{{strtoupper(trans('accountent.billing.qty'))}}</th>
            <th>{{strtoupper(trans('accountent.billing.total'))}}</th>
          </tr>
        </thead>
        <tbody>
            @php($k = 0)
          @foreach($bill->billingLines as $line)
            <tr>
              <td class="desc">{{$line->designation}}</td>
              <td class="unit">{{$line->amountUnit}}</td>
              <td class="qty">{{$line->nb_users}}</td>
              <td class="total">{{$line->nb_users*$line->amountUnit}}</td>
              @php($k++)
              <!-- <td>{{--$line->nb_users*$line->amountUnit - $line->nb_users*$line->amountUnit*($line->tva/100)--}}</td> -->
            </tr>
          @endforeach
          @for ($i = $k; $i < 7; $i++)
            <tr>
              <td class="service" colspan="5">&nbsp;</td>
            </tr>
          @endfor
          <!-- <tr>
            <td class="service" colspan="5">&nbsp;</td>
          </tr>
          <tr>
            <td class="service" colspan="5">&nbsp;</td>
          </tr>
          <tr>
            <td class="service" colspan="5">&nbsp;</td>
          </tr> -->
          <tr>
            <td colspan="3">Total HT</td>
            <td class="total">€ {{$ht}}</td>
          </tr>
          <tr>
            <td colspan="3">TVA {{$line->tva}}%</td>
            <td class="total">€ {{$ttc-$ht}}</td>
          </tr>
          <tr>
            <td colspan="3" class="grand total">TOTAL</td>
            <td class="grand total">€ {{$ttc}}</td>
          </tr>
        </tbody>
          <tfoot></tfoot>
      </table>
    </main>
    <footer>
      {{trans('accountent.billing.footer')}}
    </footer>
  </body>
</html>