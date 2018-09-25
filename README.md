# SMS_Center
Projeto de Envio de SMS (Individual e em Massa) para uso em Asterisk (Issabel).

## TODO
- Automatizar Listagem dos Modens
- Traduzir Mensagem de Retorno
- Concluir seção 'Em Massa'

## Requisitos
- Possuir um Issabel com Chan_dongle instalado, funcional e com modens ativos já em funcionamento.

## Instalação em Issabel
- Alterar os values dos Options para os nomes usados no sistema
```
<option value="modulo01" default>Chip 1</option>
```
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

## Sobre
- O projeto utiliza o CDN do bootstrap 4.
- CSS e JS estão incorporados ao final do arquivo.

## Autor
- Renato Siqueira