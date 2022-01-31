<!DOCTYPE html>
<html lang="en"><head>        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">        
        <style type="text/css">  
            header {
                position: fixed;
                top: -20px;
                left: 0px;
                right: 0px;
                height: 90px;
                /*border: solid red;
                border-width: thin;*/
            }
            footer {
                position: fixed; 
                bottom: -20px; 
                left: 0px; 
                right: 0px;
                height: 20px;
                text-align:center;
                /*border: solid red;
                border-width: thin;*/                
            }            
            body {
                margin-top: 70px;
                margin-bottom: 5px;
                font-family: "Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;
                font-size: 10px;
                /*border: solid blue;
                border-width: thin;*/                
            }
            @page {
                margin-top: 3.0em;
                margin-right: 3.0em;
                margin-left: 3.0em;
                margin-bottom: 3.0em;
            }
            small {
                font-size: smaller;
            }    
            .saltopagina{page-break-after:always;
            }
            .text-center {
                text-align: center;
            }
            .text-left {
                text-align: left;
            }
            .text-right {
                text-align: right;
            }
            .well {
                min-height: 20px;
                padding: 9px;
                margin-bottom: 20px;
                background-color: #f5f5f5;
                border: 1px solid #e3e3e3;
                border-radius: 4px;
                -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
            }
            .symbols{
                font-family:"DeJaVu Sans Mono",monospace;
            }
        </style>
    </head><body>                        
        @stack('stylesheets')                
        <header>
            <table class="table" width="100%" border="0">
                <tbody>
                    <tr>
                        <td width="25%">
                            <img alt="image" style="max-height:80px; max-width:100px;" src="{{ $logo }}"/>
                        </td>
                        <td width="50%" style="text-align:center">
                            <h2>{{ $company }}</h2>
                        </td>
                        <td width="25%" style="text-align:left;color:grey"></td>
                    </tr>
                </tbody>
            </table>
        </header>
        <footer>
            Copyright &copy; {{ date("Y") }} {{ config('app.name') }}. Todos los derechos reservados.
        </footer>        
            @yield('content')
        @stack('scripts')
    </body></html>