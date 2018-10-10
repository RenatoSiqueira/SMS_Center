<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Central SMS">
    <meta name="author" content="Renato Siqueira">
    <title>Enviar SMS</title>
</head>
<body>
        <?php
            $destino 	= $_POST[destino];
            $mensagem 	= $_POST[mensagem];
            $canal 		= $_POST[canal];

            $command="asterisk -rx ";
            if($destino && $mensagem && $canal){
                $output = shell_exec("$command \"dongle sms $canal $destino $mensagem \" ");
            }

            /* Retorno do Envio */
            $retorno = nl2br($output);
            
            /* Lendo dongles.conf */
            $lines = file ('/etc/asterisk/dongle.conf');
        ?>

        <!-- MENU -->
        <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
            <header class="masthead mb-auto">
            <div class="inner">
                <h3 class="masthead-brand">SMS Center</h3>
                <nav class="nav nav-masthead justify-content-center">
                <a class="nav-link" href="#void" onclick="changeOption('individual')">Individual</a>
                <!-- <a class="nav-link" href="#void" onclick="changeOption('emMassa')">Em Série</a> -->
                </nav>
            </div>
            </header>

            <!-- SMS Individual -->
            <main role="main" class="inner cover" id="SMSIndividual">
                <h1 class="cover-heading">Envio de SMS</h1>
                <p class="lead">
                    <form class="form-horizontal" method="POST">
                        <fieldset>
                            <?php if($retorno) { ?>
                            <div class="form-group col-md-12">
                                <div class="alert alert-info">
                                    <h4 class="alert-heading"><?php echo "$destino"; ?></h4>
                                    <p><?php echo "$mensagem"; ?></p>
                                    <hr>
                                    <p class="mb-0"><?php echo "$retorno"; ?></p>
                                </div>
                            </div>
                            <?php } ?>

                       
                            <!-- Select Chip -->
                            <div class="form-group">
                                <label class="col-md-12 control-label" for="selectChip">Selecione o Chip</label>
                                <div class="col-md-12">
                                    <select id="selectChip" name="canal" class="form-control">

                                    <?php
                                        foreach ($lines as $line_num => $line) {
                                            if ( preg_match("/^\[.+\]/", $line, $matches) ) {
                                                if ( $matches[0] != '[general]' && $matches[0] != '[defaults]' && $matches[0] != '[device]' ) {
                                                    $nomeDongle = substr($matches[0], 0, -1);
                                                    $nomeDongle = substr($nomeDongle, 1);
                                                    echo "<option value='" . $nomeDongle . "'>" . $nomeDongle . "</option>";
                                                }
                                            }
                                        }
                                    ?>

                                    </select>
                                </div>
                            </div>
                        
                            <!-- Client Number -->
                            <div class="form-group">
                                <label class="col-md-12 control-label" for="textinput">Nº Cliente?</label>  
                                <div class="col-md-12">
                                    <input 
                                        id="textinput" name="destino" type="number"
                                        placeholder="91983730000" class="form-control input-md"
                                        required title="Digite somente o DDD+Número. Sem pontuações e/ou traços."/>
                                    <span class="help-block"><small>Preencher apenas DDD+Nº (Apenas Números)</small></span>
                                </div>
                            </div>

                            <!-- Message to Send -->
                            <div class="form-group">
                                <label class="col-md-12 control-label" for="textarea">Mensagem</label>
                                <div class="col-md-12">
                                    <textarea class="form-control" id="TxtObservacoes" name="mensagem" rows="5">Olá! Sua Ordem de Serviço é XXXX. Sua instalação está pré-agendado para o dia __/__/__.</textarea>
                                    <span class="help-block"><span data-js="restantes">140</span> Restantes</span>
                                </div>
                            </div>
            
                            <!-- Send! -->
                            <div class="form-group">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">Enviar</button>
                                </div>
                            </div>
                        
                        </fieldset>
                    </form>
                </p>
            </main>

            <!-- SMS Em Massa -->
            <main role="main" class="inner cover" id="SMSEmMassa" style="display: none;">
                <h1 class="cover-heading">Envio de SMS em Massa</h1>
                <p class="lead">
                    <form class="form-horizontal" method="POST">
                        <fieldset>
                            <?php if($retorno) { ?>
                            <div class="form-group col-md-12" style="display:none;">
                                <div class="alert alert-info">
                                    <h4 class="alert-heading"><?php echo "$destino"; ?></h4>
                                    <p><?php echo "$mensagem"; ?></p>
                                    <hr>
                                    <p class="mb-0"><?php echo "$retorno"; ?></p>
                                </div>
                            </div>
                            <?php } ?>
                        
                            <!-- Select Chip -->
                            <div class="form-group">
                                <label class="col-md-12 control-label" for="selectChip">Selecione os Chips</label>
                                <div class="col-md-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                        <label class="form-check-label" for="defaultCheck1">
                                            Chip 1
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck2">
                                        <label class="form-check-label" for="defaultCheck2">
                                            Chip 2
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                        <label class="form-check-label" for="defaultCheck1">
                                            Chip 3
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck2">
                                        <label class="form-check-label" for="defaultCheck2">
                                            Chip 4
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                        <label class="form-check-label" for="defaultCheck1">
                                            Chip 5
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck2">
                                        <label class="form-check-label" for="defaultCheck2">
                                            Chip 6
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                        <label class="form-check-label" for="defaultCheck1">
                                            Chip 7
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck2">
                                        <label class="form-check-label" for="defaultCheck2">
                                            Chip 8
                                        </label>
                                    </div>
                                </div>
                            </div>
                        
                            <!-- Client Number -->
                            <div class="form-group">
                                <label class="col-md-12 control-label" for="textinput">Selecione o Arquivo:</label>  
                                <div class="col-md-12">
                                    <input type="file" name="uploadFile" id="" class="form-control-file">
                                    <span class="help-block"><small>O Arquivo deve ter números separados por vírgula (Ex. 9430000000,92980000000)</small></span>
                                </div>
                            </div>

                            <!-- Message to Send -->
                            <div class="form-group">
                                <label class="col-md-12 control-label" for="textarea">Mensagem</label>
                                <div class="col-md-12">
                                    <textarea class="form-control" id="TxtObservacoes1" name="mensagem" rows="5">Olá! Sua Ordem de Serviço é XXXX. Sua instalação está pré-agendado para o dia __/__/__.</textarea>
                                    <span class="help-block"><span data-js="restantes1">140</span> Restantes</span>
                                </div>
                            </div>
            
                            <!-- Send! -->
                            <div class="form-group" style="display: none;">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">Enviar</button>
                                </div>
                            </div>
                        
                        </fieldset>
                    </form>
                </p>
            </main>
    
            <footer class="mastfoot mt-auto">
            <div class="inner">
                <p>SMS Center 2018.</p>
            </div>
            </footer>
        </div>

    <?php
        /* Reset */
        $destino        = "";
        $mensagem       = "";
        $canal          = "";
    ?>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        * { margin: 0; padding: 0; font-family:Tahoma; font-size:12pt; }
        
        /* Body */
        html, body {
            height: 100%;
            background-color: #333;
        }
        body {
            display: -ms-flexbox;
            display: flex;
            color: #fff;
            text-shadow: 0 .05rem .1rem rgba(0, 0, 0, .5);
            box-shadow: inset 0 0 5rem rgba(0, 0, 0, .5);
        }

        /* Container */
        .cover-container { max-width: 42em; }

        /* Header */
        .masthead { margin-bottom: 2rem; }
        .masthead-brand { margin-bottom: 0; }
        .nav-masthead .nav-link {
            padding: .25rem 0;
            font-weight: 700;
            color: rgba(255, 255, 255, .5);
            background-color: transparent;
            border-bottom: .25rem solid transparent;
        }
        .nav-masthead .nav-link:hover,.nav-masthead .nav-link:focus { border-bottom-color: rgba(255, 255, 255, .25); }
        .nav-masthead .nav-link + .nav-link { margin-left: 1rem; }
        .nav-masthead .active { color: #fff; border-bottom-color: #fff; }
        @media (min-width: 48em) {
            .masthead-brand { float: left; }
            .nav-masthead { float: right; }
        }

        /* Heading */
        .cover-heading { text-align: center; }

        /* Footer */
        .mastfoot { color: rgba(255, 255, 255, .5); }

        /* Remove Arrows from input:number */
        input[type=number]::-webkit-inner-spin-button { 
            -webkit-appearance: none;
            cursor:pointer;
            display:block;
            width:8px;
            color: #333;
            position:relative;
        }
        input[type=number] { 
        -moz-appearance: textfield;
        appearance: textfield;
        margin: 0; 
        }
    </style>

    <script>
        function changeOption(option) {
            if(option === 'individual') {
                document.getElementById('SMSIndividual').style.display = 'block';
                document.getElementById('SMSEmMassa').style.display = 'none';
            }

            if(option === 'emMassa') {
                document.getElementById('SMSIndividual').style.display = 'none';
                document.getElementById('SMSEmMassa').style.display = 'block';
            }
        }

        (function(doc){
            var $txtObservacoes = doc.querySelector("#TxtObservacoes")
            var $restantes = doc.querySelector("[data-js='restantes']")
            
            $txtObservacoes.addEventListener("input", function() {
                var limite = 140;
                var caracteresDigitados = $txtObservacoes.value.length;
                $restantes.innerHTML= limite - caracteresDigitados;
            }, false)
        })(document)
    </script>
</body>
</html>