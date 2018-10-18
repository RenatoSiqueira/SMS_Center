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

            /* Nome .ini Default */
            $nameArquivo = 'sms.ini';

            /* Lendo dongles.conf */
            $lines = file ('/etc/asterisk/dongle.conf');

            /* Comando Padrão Asterisk */
            $command="asterisk -rx ";

            //////////////////////////////////////////////////////////////////////
            /** https://github.com/uzi88/PHP_INI_Read_Write */
            class INI
            {
                /** INI file path
                 * @var String
                 */
                var $file = NULL;
                
                /** INI data
                 * @var Array
                 */
                var $data = array();
                
                /** Process sections
                 * @var Boolean
                 */
                var $sections = TRUE;
                
                /** Parse INI file
                 * @param 	String		$file 		- INI file path
                 * @param 	Boolean		$sections 	- Process sections
                 */
                function INI() {
                    if (func_num_args()) {
                        $args = func_get_args();
                        call_user_func_array(array($this, 'read'), $args);
                    }
                }
                
                /** Parse INI file
                 * @param 	String		$file 		- INI file path
                 * @param 	Boolean		$sections 	- Process sections
                 */
                function read($file = NULL, $sections = TRUE) {
                    $this->file = ($file) ? $file : $this->file;
                    $this->sections = $sections;
                    $this->data = parse_ini_file(realpath($this->file), $this->sections);
                    return $this->data;
                }
                
                /** Write INI file
                 * @param 	String		$file 		- INI file path
                 * @param 	Array		$data 		- Data (Associative Array)
                 * @param 	Boolean		$sections 	- Process sections
                 */
                function write($file = NULL, $data = array(), $sections = TRUE) {
                    $this->data = (!empty($data)) ? $data : $this->data;
                    $this->file = ($file) ? $file : $this->file;
                    $this->sections = $sections;
                    $content = NULL;
                    
                    if ($this->sections) {
                        foreach ($this->data as $section => $data) {
                            $content .= '[' . $section . ']' . PHP_EOL;
                            foreach ($data as $key => $val) {
                                if (is_array($val)) {
                                    foreach ($val as $v) {
                                        $content .= $key . '[] = ' . (is_numeric($v) ? $v : '"' . $v . '"') . PHP_EOL;
                                    }
                                } elseif (empty($val)) {
                                    $content .= $key . ' = ' . PHP_EOL;
                                } else {
                                    $content .= $key . ' = ' . (is_numeric($val) ? $val : '"' . $val . '"') . PHP_EOL;
                                }
                            }
                            $content .= PHP_EOL;
                        }
                    } else {
                        foreach ($this->data as $key => $val) {
                            if (is_array($val)) {
                                foreach ($val as $v) {
                                    $content .= $key . '[] = ' . (is_numeric($v) ? $v : '"' . $v . '"') . PHP_EOL;
                                }
                            } elseif (empty($val)) {
                                $content .= $key . ' = ' . PHP_EOL;
                            } else {
                                $content .= $key . ' = ' . (is_numeric($val) ? $val : '"' . $val . '"') . PHP_EOL;
                            }
                        }
                    }
                    
                    return (($handle = fopen($this->file, 'w')) && fwrite($handle, trim($content)) && fclose($handle)) ? TRUE : FALSE;
                }
            }
            /* Instanciando Classe */
            $ini = new INI($nameArquivo);
            //////////////////////////////////////////////////////////////////////

            /* SE: Criando Nova Mensagem */
            $identificacaoMsg = $_POST[identificacaoMsg];
            if($identificacaoMsg) {
                $novaMensagem 	= str_replace(array("\n","\r"," "), " ", $_POST[novaMensagem]);
                $ini->data['mensagens'][$identificacaoMsg] = $novaMensagem;
                $ini->write();
            }

            /* Lendo INI file */
            $smsIni = parse_ini_file($nameArquivo, true);
            if(!$smsIni) {
                print "Nao Existe. Criar sms.ini com permissões 777. ";
                exit;
            }

            /* Recebendo/Tratando SMS para Enviar */
            $destino 	= $_POST[destino];
//            $mensagem 	= str_replace(array("\n","\r"," "), " ", $_POST[mensagem]);
            $mensagem 	= $_POST[mensagem];
            $canal 		= $_POST[canal];
            if($destino && $mensagem && $canal){
                $output = shell_exec("$command \"dongle sms $canal $destino $mensagem \" ");
            }

            function msgAlert($tipo, $mensagemRetorno, $destino, $mensagem) {
                return '
                    <div class="form-group col-md-12">
                        <div class="alert alert-' . $tipo . '">
                            <h4 class="alert-heading">' . $destino . '</h4>
                            <p>' . $mensagem . '</p>
                            <hr>
                            <p class="mb-0"><strong>' . $mensagemRetorno . '</strong></p>
                        </div>
                    </div>
                ';
            }

            /* Retorno do Envio (resposta do modem) */
            $posDisabled = strpos( $output, 'disabled' );
            $posError = strpos( $output, 'error' );
            $posQueued = strpos( $output, 'queued' );

            if($posDisabled)
                $retorno = msgAlert('warning', 'Erro:<br/> Modem Desativado/Desligado. ' . nl2br($output), $destino, $mensagem);

            if($posError)
                $retorno = msgAlert('warning', 'Erro:<br/> Não Foi Possível Enviar. ' . nl2br($output), $destino, $mensagem);

            if($posQueued) {
                $retorno = msgAlert('success', 'Mensagem encaminhada para Enviar.', $destino, $mensagem);
                /* Update qtde Msg Enviadas */
                $ini->data['qtdeSms'][$canal] = intval($smsIni['qtdeSms'][$canal]) + 1;
                $ini->write();
            }

            /** Atualizando Informações. */
            $smsIni = parse_ini_file($nameArquivo, true);
        ?>

        <!-- MENU -->
        <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
            <header class="masthead mb-auto">
            <div class="inner">
                <h3 class="masthead-brand">SMS Center</h3>
                <nav class="nav nav-masthead justify-content-center">
                <a class="nav-link" href="#void" onclick="changeOption('individual')">Individual</a>
                <!-- <a class="nav-link" href="#void" onclick="changeOption('emMassa')">Em Série</a> -->
                <a class="nav-link" href="#void" onclick="changeOption('gerenciamentoMsg')">Mensagens</a>
                </nav>
            </div>
            </header>

            <!-- SMS Individual -->
            <main role="main" class="inner cover" id="SMSIndividual">
                <h1 class="cover-heading">Envio de SMS</h1>
                <p class="lead">
                    <form class="form-horizontal" method="POST">
                        <fieldset>

                            <?php if($retorno) { echo $retorno; } ?>

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
                                                    
                                                    $qtdeSmsModem = $smsIni['qtdeSms'][$nomeDongle];
                                                    if($qtdeSmsModem) { 
                                                        $qtdeSmsModem = " - " . $qtdeSmsModem . " Mensagen(s) Enviada(s)";
                                                    }

                                                    echo "<option value='" . $nomeDongle . "'>" . $nomeDongle . $qtdeSmsModem . "</option>";
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
                                    <select id="selectMsg" class="form-control" onChange="changeMsgs(this.value);">

                                        <?php
                                            foreach($smsIni['mensagens'] as $indice => $valor)
                                            {
                                                echo "<option value='" . $indice . "'>" . $indice . "</option>";
                                            }
                                        ?>

                                    </select>
                                    <br/>
                                    <input class="form-control" id="TxtObservacoes" name="mensagem" value="Olá! Sua Ordem de Serviço é XXXX. Sua instalação está pré-agendado para o dia __/__/__.">
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

                                    <?php
                                        foreach ($lines as $line_num => $line) {
                                            if ( preg_match("/^\[.+\]/", $line, $matches) ) {
                                                if ( $matches[0] != '[general]' && $matches[0] != '[defaults]' && $matches[0] != '[device]' ) {
                                                    $nomeDongle = substr($matches[0], 0, -1);
                                                    $nomeDongle = substr($nomeDongle, 1);
                                                    echo "
                                                    <div class='form-check form-check-inline'>
                                                    <input class='form-check-input' type='checkbox' value='' id='" . $nomeDongle . "'>
                                                    <label class='form-check-label' for='" . $nomeDongle . "'>
                                                        " . $nomeDongle . "
                                                    </label>
                                                    </div>
                                                    ";
                                                }
                                            }
                                        }
                                    ?>

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

            <!-- Gerenciamento de Mensagens -->
            <main role="main" class="inner cover" id="gerenciamentoMsg" style="display: none;">
                <h1 class="cover-heading">Cadastrar Mensagens</h1>
                <p class="lead">
                    <form class="form-horizontal" method="POST">
                        <fieldset>
                            <div class="form-group">
                                <label class="col-md-12 control-label" for="textarea">Nova Mensagem</label>
                                <div class="col-md-12">
                                    <input type="text" placeholder="Mensagem de Agradecimento" name="identificacaoMsg" class="form-control" required>
                                </div>
                                <br />
                                <div class="col-md-12">
                                    <textarea class="form-control" id="novaMensagem" rows="5" name="novaMensagem" placeholder="Qual mensagem deseja cadastrar?" required></textarea>
                                    <span class="help-block"><span data-js="restantesNovaMensagem">140</span> Restantes</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">Salvar</button>
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
        }

        /* Container */
        .cover-container { max-width: 42em; }

        /* Header */
        .masthead { margin-bottom: 2rem; }
        .masthead-brand { margin-bottom: 0; }
        .nav-masthead .nav-link {
            padding: .25rem 0;
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
            event.preventDefault();
            
            if(option === 'individual') {
                document.getElementById('SMSIndividual').style.display = 'block';
                document.getElementById('SMSEmMassa').style.display = 'none';
                document.getElementById('gerenciamentoMsg').style.display = 'none';
            }

            if(option === 'emMassa') {
                document.getElementById('SMSIndividual').style.display = 'none';
                document.getElementById('SMSEmMassa').style.display = 'block';
                document.getElementById('gerenciamentoMsg').style.display = 'none';
            }

            if(option === 'gerenciamentoMsg') {
                document.getElementById('SMSIndividual').style.display = 'none';
                document.getElementById('SMSEmMassa').style.display = 'none';
                document.getElementById('gerenciamentoMsg').style.display = 'block';
            }
        }

        function changeMsgs(option) {
            <?php
                foreach($smsIni['mensagens'] as $indice => $valor) {
                    echo "
                        if('" . $indice . "' == option) {
                            document.querySelector('#TxtObservacoes').value = '" . $smsIni['mensagens'][$indice] . "';
                        }
                    ";
                }
            ?>
        }

        (function(doc){
            var limite = 140;
            
            var $txtObservacoes = doc.querySelector("#TxtObservacoes");
            var $restantes = doc.querySelector("[data-js='restantes']");
            var $selectMsg = doc.querySelector("[data-js='selectMsg']");

            var $novaMensagem = doc.querySelector("#novaMensagem");
            var $restantesNovaMensagem = doc.querySelector("[data-js='restantesNovaMensagem']");
            
            $txtObservacoes.addEventListener("input", function() {
                var caracteresDigitados = $txtObservacoes.value.length;
                $restantes.innerHTML= limite - caracteresDigitados;
            }, false);

            $novaMensagem.addEventListener("input", function() {
                var caracteresDigitados = $novaMensagem.value.length;
                $restantesNovaMensagem.innerHTML= limite - caracteresDigitados;
            }, false);

        })(document)
    </script>

</body>
</html>