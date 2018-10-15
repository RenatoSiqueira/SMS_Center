# SMS_Center
Projeto de Envio de SMS (Individual e em Massa) para uso em Asterisk (Issabel).

## TODO
- **Concluído:** Automatizar Listagem dos Modens.
- **Concluído:** Opção de Cadastro de Mensagens.
- **Concluído:** Acompanhamento de Qtde Enviada em cada Modem.
- Traduzir Mensagem de Retorno
- Concluir seção 'Em Massa'
- Gerenciar Mensagens cadastradas: Editar/Apagar

## Requisitos
- Possuir um Issabel com Chan_dongle instalado, funcional e com modens ativos já em funcionamento.

## Instalação em Issabel
- Criar uma pasta SMS (sugestão) em:
```
/var/www/html/
```
- colocar os arquivos index.php e sms.ini dentro.
- Setar permissões no sms.ini
```
chmod +777 /var/www/html/sms/sms.ini
```

## Modo de Uso
- Acesse IP_Servidor/SMS

Ex:
```
https://192.168.0.3/sms
```

## Outras Informações
- O index.php faz uma leitura do arquivo dongle.conf para identificar cada dongle de forma automática.

## Sobre
- O projeto utiliza o CDN do bootstrap 4.
- CSS e JS estão incorporados ao final do arquivo.
- Obrigado ao @uzi88 pela class manipuladora de INI: https://github.com/uzi88/PHP_INI_Read_Write

## Autor
- Renato Siqueira