# Ifthenpay Joomla/Virtuemart payment gateway

Ler em :portugal: [Português](readme.pt.md), e :gb:[Inglês](readme.md)

[1. Introdução](#Introdução)

[2. Compatibilidade](#Compatibilidade)

[2. Instalação](#Instalação)

[3. Configuração](#Configuração)

[4. Experiência do Utilizador Consumidor](#Experiência-do-Utilizador-Consumidor)


# Introdução
![Ifthenpay](https://ifthenpay.com/images/all_payments_logo_final.png)

**Este é o plugin Ifthenpay para Joomla com componente de E-Commerce Virtuemart**

**Multibanco** é um método de pagamento que permite ao consumidor pagar com referência bancária.
Este módulo permite gerar referências de pagamento que o consumidor pode usar para pagar a sua encomenda numa caixa multibanco ou num serviço online de Home Banking. Este plugin usa a Ifthenpay, uma das várias gateways disponíveis em Portugal.

**MB WAY** é a primeira solução inter-bancos que permite a compra e transferência imediata por via de smartphone e tablet.
Este módulo permite gerar um pedido de pagamento ao smartphone do consumidor, e este pode autorizar o pagamento da sua encomenda na aplicação MB WAY. Este plugin usa a Ifthenpay, uma das várias gateways disponíveis em Portugal.

**Payshop** é um método de pagamento que permite ao consumidor pagar com referência payshop.
Este módulo permite gerar uma referência de pagamento que o consumidor pode usar para pagar a sua encomenda num agente Payshop ou CTT. Este plugin usa a Ifthenpay, uma das várias gateways disponíveis em Portugal.

**Cartão de Crédito** Este módulo permite gerar um pagamento por Visa ou Master card, que o consumidor pode usar para pagar a sua encomenda. Este plugin usa a Ifthenpay, uma das várias gateways disponíveis em Portugal.

**É necessário contrato com a Ifthenpay**

Mais informações em [Ifthenpay](https://ifthenpay.com). 


# Compatibilidade

Use a tabela abaixo para verificar a compatibilidade do plugin Ifthenpay gateway com a sua loja online.
|  | Joomla 3 + virtuemart 4 | Joomla 4 + Virtuemart 4 |
|---|---|---|
| Ifthenpay v1.0.0 | Compatível | Compatível |

# Instalação

Siga os passos indicados abaixo para instalar e configurar o plugin:

* clique no link para aceder à última versão
![extensions_install](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/get_latest_release.png)
</br>

* descarregue o ficheiro zip de instalação
![extensions_install](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/en/download_installer.png)
</br>

* no menu do topo no backoffice do Joomla selecione Extensões/Gerir/Instalar
![extensions_install](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/extensions_install.png)
</br>

* arraste o ficheiro zip, descarregado anteriormente, para a caixa que diz "Arraste e solte aqui os ficheiros..."
![drag_install](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/drag_install.png)
</br>

* no menu do topo no backoffice do Joomla selecione VirtueMart/Payment Methods
![view_payment_methods](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/view_payment_methods.png)
</br>

* click no botão " + Novo "
![new_payment_method](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/new_payment_method.png)
</br>

* preencha a informação do método de pagamento e clique no botão "Guardar"
1. **Payment Name** - Insira "Ifthenpay"
2. **Sef Alias** - Insira "Ifthenpay"
3. **Published** - Selecione "Sim" se deseja disponiblizar o método de pagamento na página de checkout
4. **Payment Description** - Este campo é opcional
5. **Payment Method** - Selecione "Ifthenpay Payments"
6. **Shopper Group** - Este campo é opcional (preencha de acordo com as necessidades da sua loja)
7. **List Order** - Este campo é opcional (preencha de acordo com as necessidades da sua loja)
8. **Currency** - Euro é selecionado por defeito, pois é única moeda suportada currentemente

![fill_information](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/fill_information.png)
</br>


# Configuração

* no menu do topo no backoffice do Joomla selecione VirtueMart/Payment Methods
![view_payment_methods](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/view_payment_methods.png)
</br>

* clique no Método de pagamento que acabou de adicionar
![select_ifthenpay](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/select_ifthenpay.png)
</br>

* abra a tab de configuração (esta tab deve abrir por defeito)
![config_tab](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/config_tab.png)
</br>

* se ainda não tem um conta Ifthenpay, pode requerir uma preenchedo o ficheiro pdf do contrato de adesão que pode descarregar ao clicar no botão "Crie uma conta agora!", e enviando este juntamente com a documentação pedida para para o email ifthenpay@ifthenpay.com 
![request_account](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/request_account.png)
</br>

* preencha a configuração do método de pagamento e clique no botão "salvar":
  
1. **Chave da gateway** - Insira a Gateway Key fornecida na conclusão do contrato, por exemplo: AAAA-999999 (quatro letras maiusculas, um ifen, e seis algarismos)
2. **Chave Anti-phishing** - Gerada automáticamente, mas pode criar uma, esta deve  conter um total de 50 caracteres alfanuméricos
3. **Substituir as imagens por texto** - Se não desejar mostrar os logos dos métodos de pagamento, pode substituir estes por uma linha de texto
4. **Encomenda Pendente** - Pending por defeito, mas pode alterar se necessário
4. **Encomenda Confirmada** - Confirmed por defeito, mas pode alterar se necessário
4. **Encomenda Cancelada** - Cancelled por defeito, mas pode alterar se necessário
7. **Países** - Deixe vazio se desejar disponibilizar o método de pagamento a todos os países, ou selecione um ou mais países para apenas disponibilizar o método de pagamento a esses países
8. **Montante mínimo** - Deixe vazio se desejar disponibilizar o método de pagamento para encomendas sem valor mínimo, ou insira um valor numérico para apenas disponibilizar o método de pagamento para encomendas com valor total superior ao mínimo definido
9. **Montante máximo** - Deixe vazio se desejar disponibilizar o método de pagamento para encomendas sem valor máximo, ou insira um valor numérico para apenas disponibilizar o método de pagamento para encomendas com valor total inferior ao máximo definido
10. **Moeda** - Euro é selecionado por defeito, pois é única moeda suportada currentemente

![config_first_part](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/config_first_part.png)
</br>

* após salvar, novos campos ficaram disponíveis:

11. **Métodos de Pagamento Disponíveis** - (apenas de leitura) mostra os logos dos métodos de pagamento disponiveis na sua gateway de pagamento
12. **Ativar** - clique para ser redirecionado para a página de ativação
13. **Url** - (apenas de leitura) URL do callback, pode usar esta para fazer testes à mudança de estado da encomenda
14. **Chave Anti-phishing** - (apenas de leitura) chave anti-phishing guardada na base de dados

![config_second_part](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/config_second_part.png)
</br>

* para ativar o callback clique em "Ativar Callback" (necessário se deseja atualizar o estado da encomenda de pending para confirmd quando o pagamento é recebido)
![config_press_activate](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/press_activate.png)
</br>

* na página do assistente de ativação do callback encontrará os campos Gateway Key e Anti-Phishing Key já preenchidos corretamente, apenas necessita de inserir a sua Backoffice Key e clicar no botão Ativar (a Backoffice Key é constituída por quatro conjuntos de quatro algarismos separados por ifen e é fornecida na conclusão do contrato com a Ifthenpay, um exemplo: 1111-2222-3333-4444)
![config_activate](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/activate.png)
</br>

* e com isto terminou a configuração do seu plugin de pagamento, agora pode experimentar do lado do consumidor.



# Experiência do Utilizador Consumidor
O seguinte é experienciado da perspectiva do cliente consumidor.
O cliente da sua loja pode pagar por uma encomenda da seguinte maneira.

1. ...após adicionar um item ao carrinho e avançar para o checkout, seleciona o método de pagamento Ifthenpay (este pode ser mostrado como um ou vários logos dos métodos de pagamentos, ou apenas uma linha de texto dependendo da sua configuração)
2. aceita os termos de acordo
3. clica o botão de confirmar compra
![customer_checkout](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/checkout.png)
</br>

(irá ser redirecionado para a página de Gateway da Ifthenpay)

4. aqui pode verificar o valor da encomenda a pagar
5. seleciona o método de pagamento dos que estão disponiveis, estes dependeram do seu contrato:
  - **Multibanco** - São disponblizados a Entidade, a Referência e o Valor, estes podem ser usados para pagar numa caixa multibanco ou através de um serviço online de Homebanking
  - **MB WAY** - (Necessário que o cliente consumidor tenha a app MB WAY instalada no smartphone) seleciona o indicativo do país e insere o número do smartphone, clica no botão pagar e receberá uma notificação para proceder ao pagamento no smartphone
  - **Payshop** - São disponblizados a Referência e o Valor, e pode usar estes para pagar nos CTT ou numa loja agente Payshop
  - **Ccard** - Insere os dados do cartão de crédito e clica no botão pagar
6. pode imprimir os dados do pagamento clicando no botão imprimir
7. após obter os dados de pagamento para métodos de pagamento ofline (ex: Multibanco e Payshop) ou terminando o pagamento usando pagamento online (ex: MB WAY e Cartão de Crédito), pode clicar no botão fechar e será redirecionado para a página de agradecimento da loja
![customer_gateway](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/gateway.png)

#### ao escolher o método de pagamento será exibido o seguinte:

* ao escolher Multibanco, serão apresentados Entidade, Referência e Valor a usar numa caixa Multibanco ou serviço online de Homebanking 
![customer_gateway_multibanco](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/multibanco.png)
</br>

* ao escolher MB WAY, será pedido o número de telefone e após inserir este e clicar em pagar, receberá uma notificação na aplicação MB WAY do smartphone (nota: o consumidor tem de ter a aplicação MB WAY instalada no smartphone associado ao número de telefone inserido)
![customer_gateway_mbway](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/mbway.png)
</br>

* ao escolher Payshop, serão apresentados a Referência Payshop e o Valor a usar em qualquer CTT ou agent Payshop
![customer_gateway_payshop](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/payshop.png)
</br>

* ao escolher Cartão de Crédito, será redirecionado para outra página onde pode preencher os dados do cartão de crédito e clicar no botão pagar para terminar
![customer_gateway_ccard](https://github.com/ifthenpay/joomla-virtuemart/raw/assets/img/pt/ccard.png)
</br>



