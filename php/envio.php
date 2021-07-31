<?php
	
	// Apenas metodos POST
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Variavel recebendo arquivo (curriculo)
        $arquivo = $_FILES['curriculo'];

        // Variaveis recebendo valores dos campos e removendo espaços vazios
        $nome = strip_tags(trim($_POST["nome"]));
        $number = strip_tags(trim($_POST["number"]));
        $nome = str_replace(array("\r","\n"),array(" "," "),$nome);
        //E-mail do usuario
        $replyto = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $message = trim($_POST["mensagem"]);

        // Variavel recebendo sexo
        $selected_sexo = $_POST['sexo'];
        if ($selected_sexo == "masc") {
            $selected_sexo = "Masculino";
        } elseif ($selected_turno == "fem") {
            $selected_turno = "Feminino";
        }

        // Variaveais Checkbox - Profissão
        $check_saude = isset($_POST['saude']) ? Sim : Nao;
        $check_tec = isset($_POST['tec']) ? Sim : Nao;
        $check_edu = isset($_POST['edu']) ? Sim : Nao;
        $check_sg = isset($_POST['sg']) ? Sim : Nao;

        /***** IFs para cada opção do checkbox marcada *****/
        if ($check_saude == "Sim" & $check_tec == "Nao" & $check_edu == "Nao" & $check_edu == "Nao") { // Se usuario marcou area da saude...
        	$profissao = "Trabalho na Área da Saúde";
        } elseif ($check_saude == "Sim" & $check_tec == "Sim" & $check_edu == "Nao" & $check_sg == "Nao") { // Se usuario marcou area da saude e de tecnologia...
        	$profissao = "Trabalho na Área da Saúde e de Tecnologia";
        } elseif ($check_saude == "Sim" & $check_tec == "Sim" & $check_edu == "Sim" & $check_sg == "Nao") { // Se usuario marcou area da saude e de tecnologia e de educacao...
        	$profissao = "Trabalho na Área da Saúde e de Tecnologia e de Educação";
        } elseif ($check_saude == "Sim" & $check_tec == "Sim" & $check_edu == "Sim" & $check_sg == "Sim") { /**** Se usuario marcou todos os checkbox ****/
        	$profissao = "Trabalho na Área da Saúde e de Tecnologia e de Educação";
        } elseif ($check_saude == "Nao" & $check_tec == "Sim" & $check_edu == "Nao" & $check_sg == "Nao") { // Se usuario marcou area de tecnologia...
        	$profissao = "Trabalho na Área de Tecnologia";
        } elseif ($check_saude == "Nao" & $check_tec == "Sim" & $check_edu == "Sim" & $check_sg == "Nao") { // Se usuario marcou area de tecnologia e de educacao...
            $profissao = "Trabalho na Área de Tecnologia e de Educação";
        } elseif ($check_saude == "Nao" & $check_tec == "Sim" & $check_edu == "Sim" & $check_sg == "Sim") { // Se usuario marcou area de tecnologia, de educacao e de s. gerais...
            $profissao = "Trabalho na Área da Saúde e de Tecnologia, de Educação e de Serviços Gerais";
        } elseif ($check_saude == "Nao" & $check_tec == "Nao" & $check_edu == "Sim" & $check_sg == "Nao") { // Se usuario marcou area de educacao...
            $profissao = "Trabalho na Área da Educação";
        } elseif ($check_saude == "Nao" & $check_tec == "Nao" & $check_edu == "Sim" & $check_sg == "Sim") { // Se usuario marcou area de educacao e de s. gerais...
            $profissao = "Trabalho na Área da Educação e de Serviços Gerais";
        } elseif ($check_saude == "Nao" & $check_tec == "Nao" & $check_edu == "Nao" & $check_sg == "Sim") { // Se usuario marcou area de s. gerais...
            $profissao = "Trabalho na Área de Serviços Gerais";
        } 

        // Deve ser um email válido do domínio (será o e-mail utilizado para enviar o formulario)
        $remetente = "envio@seudominio.com.br";

        // Assunto do e-mail.
        $assunto = "Envio de Curriculo de $nome";

        /* Cabeçalho da mensagem  */
        $boundary = "XYZ-" . date("dmYis") . "-ZYX";
        $headers = "MIME-Version: 1.0\n";
        $headers.= "From: $remetente\n";
        $headers.= "Reply-To: $replyto\n";
        $headers.= "Content-type: multipart/mixed; boundary=\"$boundary\"\r\n";  
        $headers.= "$boundary\n";

        /* Layout da mensagem  */
        $corpo_mensagem = "
<br>--------------------------------------------<br>
<br><strong>Nome:</strong> $nome<br>
<br><strong>Email:</strong> $replyto<br>
<br><strong>Contato:</strong> $number<br>
<br><strong>Sexo:</strong> $selected_sexo<br>
<br><strong>Profissão:</strong> $profissao<br><br>
<br><strong>Mensagem:</strong> $message<br>
<br><br>--------------------------------------------";

        /* Função que codifica o anexo para poder ser enviado na mensagem  */
        if(file_exists($arquivo["tmp_name"]) and !empty($arquivo)){
         
            $fp = fopen($_FILES["arquivo"]["tmp_name"],"rb"); // Abri o arquivo enviado.
         $anexo = fread($fp,filesize($_FILES["arquivo"]["tmp_name"])); // Le o arquivo aberto na linha anterior
         $anexo = base64_encode($anexo); // Codifica os dados com MIME para o e-mail 
         fclose($fp); // Fecha o arquivo aberto anteriormente
            $anexo = chunk_split($anexo); // Divide a variável do arquivo em pequenos pedaços para poder enviar
            $mensagem = "--$boundary\n"; // Nas linhas abaixo possuem os parâmetros de formatação e codificação, juntamente com a inclusão do arquivo anexado no corpo da mensagem
            $mensagem.= "Content-Transfer-Encoding: 8bits\n"; 
            $mensagem.= "Content-Type: text/html; charset=\"utf-8\"\n\n";
            $mensagem.= "$corpo_mensagem\n"; 
            $mensagem.= "--$boundary\n"; 
            $mensagem.= "Content-Type: ".$arquivo["type"]."\n";  
            $mensagem.= "Content-Disposition: attachment; filename=\"".$arquivo["name"]."\"\n";  
            $mensagem.= "Content-Transfer-Encoding: base64\n\n";  
            $mensagem.= "$anexo\n";  
            $mensagem.= "--$boundary--\r\n"; 
        }
        else // Caso não tenha anexo
        {
            $mensagem = "--$boundary\n"; 
            $mensagem.= "Content-Transfer-Encoding: 8bits\n"; 
            $mensagem.= "Content-Type: text/html; charset=\"utf-8\"\n\n";
            $mensagem.= "$corpo_mensagem\n";
        }

        // Check that data was sent to the mailer.
        if ( empty($nome) OR empty($number) OR !filter_var($replyto, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo '<script type="text/javascript">
                alert("Opa! Algo deu errado.");
                window.history.go(-1);
            </script>';
        }

        /* Função que envia a mensagem  */
        if(mail($to, $assunto, $mensagem, $headers))
        {
            http_response_code(200);
            echo '<script type="text/javascript">
                alert("Salvo com Sucesso !");
                window.history.go(-1);
            </script>';
        } 
        else
        {
            http_response_code(500);
            echo '<script type="text/javascript">
                alert("Opa! Algo deu errado.");
                window.history.go(-1);
            </script>';
        }

    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo '<script type="text/javascript">
                alert("Opa! Algo deu errado.");
                window.history.go(-1);
        </script>';
    }

?>