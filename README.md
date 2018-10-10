# SMS_Center
Projeto de Envio de SMS (Individual e em Massa) para uso em Asterisk (Issabel).

## TODO
- **Concluído:** Automatizar Listagem dos Modens.
- Traduzir Mensagem de Retorno
- Concluir seção 'Em Massa'

## Requisitos
- Possuir um Issabel com Chan_dongle instalado, funcional e com modens ativos já em funcionamento.

## Instalação em Issabel
- Criar uma pasta SMS (sugestão) em:
```
/var/www/html/
```
- colocar o arquivo index.php dentro.

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

## Autor
- Renato Siqueira