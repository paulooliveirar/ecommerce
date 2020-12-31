       $(document).ready(function() {

            function limpa_formulário_cep() {
                // Limpa valores do formulário de cep.
                $("#descountry").val("");
                $("#desstate").val("");
                $("#desaddress").val("");
                $("#descity").val("");
            }
            
            //Quando o campo cep perde o foco.
            $("#deszipcode").blur(function() {

                //Nova variável "cep" somente com dígitos.
                var cep = $(this).val().replace(/\D/g, '');

                //Verifica se campo cep possui valor informado.
                if (cep != "") {

                    //Expressão regular para validar o CEP.
                    var validacep = /^[0-9]{8}$/;

                    //Valida o formato do CEP.
                    if(validacep.test(cep)) {

                        //Preenche os campos com "..." enquanto consulta webservice.
                        $("#descountry").val("...");
                        $("#desstate").val("...");
                        $("#descity").val("...");
                        $("#desaddress").val("...");

                        //Consulta o webservice viacep.com.br/
                        $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                            if (!("erro" in dados)) {
                                //Atualiza os campos com os valores da consulta.
                                $("#desaddress").val(dados.logradouro);
                                $("#descity").val(dados.localidade);
                                $("#desstate").val(dados.uf);
                                $("#descountry").val("Brasil");
                            } //end if.
                            else {
                                //CEP pesquisado não foi encontrado.
                                limpa_formulário_cep();
                                $("#deszipcode").css("border-color","red");
                                $("#deszipcode").after("<span id='error-msg'>CEP não encontrado. </span>");
                            }
                        });
                    } //end if.
                    else {
                        //cep é inválido.
                        limpa_formulário_cep();
                        $("#deszipcode").css("border-color","red");
                        $("#deszipcode").after("<span id='error-msg'>CEP não encontrado. </span>");
                    }
                    
                    $("#deszipcode").css("border-color","#d2d6de");
                    $("#error-msg").html("");

                } //end if.
                else {
                    //cep sem valor, limpa formulário.
                    limpa_formulário_cep();
                }
            });
        });