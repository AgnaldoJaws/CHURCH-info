<?php
/*******************************************************************************
 *
 *  filename    : PersonEditor.php
 *  website     : http://www.churchcrm.io
 *  copyright   : Copyright 2001, 2002, 2003 Deane Barker, Chris Gebhardt
 *                Copyright 2004-2005 Michael Wilt
 *
 *  ChurchCRM is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 ******************************************************************************/

//Include the function library
require "Include/Config.php";
require "Include/Functions.php";


//Set the page title
$sPageTitle = gettext("Adicionar Despesa");

//Get the PersonID out of the querystring
if (array_key_exists ("PersonID", $_GET))
	$iPersonID = FilterInput($_GET["PersonID"],'int');
else
	$iPersonID = 0;

$sPreviousPage = "";
if (array_key_exists ("previousPage", $_GET))
	$sPreviousPage = FilterInput ($_GET["previousPage"]);

// Security: User must have Add or Edit Records permission to use this form in those manners
// Clean error handling: (such as somebody typing an incorrect URL ?PersonID= manually)
if ($iPersonID > 0)
{
	$sSQL = "SELECT per_fam_ID FROM person_per WHERE per_ID = " . $iPersonID;
	$rsPerson = RunQuery($sSQL);
	extract(mysql_fetch_array($rsPerson));

	if (mysql_num_rows($rsPerson) == 0)
	{
		Redirect("Menu.php");
		exit;
	}

	if ( !(
	       $_SESSION['bEditRecords'] ||
	       ($_SESSION['bEditSelf'] && $iPersonID==$_SESSION['iUserID']) ||
	       ($_SESSION['bEditSelf'] && $per_fam_ID>0 && $per_fam_ID==$_SESSION['iFamID'])
		  )
	   )
	{
		Redirect("Menu.php");
		exit;
	}
}
elseif (!$_SESSION['bAddRecords'])
{
	Redirect("Menu.php");
	exit;
}
// Get Field Security List Matrix
$sSQL = "SELECT * FROM list_lst WHERE lst_ID = 5 ORDER BY lst_OptionSequence";


while ($aRow = mysql_fetch_array($rsSecurityGrp))
{
	extract ($aRow);
	$aSecurityType[$lst_OptionID] = $lst_OptionName;
}

// Get the list of custom person fields
$sSQL = "SELECT person_custom_master.* FROM person_custom_master ORDER BY custom_Order";

$numCustomFields = mysql_num_rows($rsCustomFields);

$date = date("d-m-Y");

//Is this the second pass?
if (isset($_POST["PersonSubmit"]) || isset($_POST["PersonSubmitAndAdd"]))
{
	//Get all the variables from the request object and assign them locally
	$notaFiscal                      = FilterInput($_POST["notaFiscal"]);
	$fornecedor                     = FilterInput($_POST["fornecedor"]);
	$descricao                       = FilterInput($_POST["descricao"]);
             $statusCaixa                    = FilterInput($_POST["statusCaixa"]);
             $tipoLancamento            = FilterInput($_POST["tipoLancamento"]);
             $dataPagamento             = FilterInput($_POST["dataPagamento"]);
	$dataVencimento            = FilterInput($_POST["dataVencimento"]);
	$dataCadastro                 = FilterInput($_POST["dataCadastro"]);
	$centroCusto                   = FilterInput($_POST["centroCusto"]);
            $tipoDespesa                    = FilterInput($_POST["tipoDespesa"]);
	$planoConta                     = FilterInput($_POST["planoConta"]);
	$tipoSaida                         = FilterInput($_POST["tipoSaida"]);
	$valor                               = FilterInput($_POST["valor"]);
	$desconto                        = FilterInput($_POST["desconto"]);
	$juros                               = FilterInput($_POST["juros"]);
	$valorTotal                      = FilterInput($_POST["valorTotal"]);
	$formaPagamento          = FilterInput($_POST["formaPagamento"]);
	$uploadComprovante      = FilterInput($_POST["uploadComprovante"]);
	$statusPagamento           = FilterInput($_POST["statusPagamento"]);
	$foiParcelado                   = FilterInput($_POST["foiParcelado"]);




			//Comando para persistir os dados no mysql
		$sSQL = "INSERT INTO Despesa (
		notaFiscal, fornecedor, descricao, statusCaixa,
		 tipoLancamento, dataPagamento, dataVencimento,
		 dataCadastro, centroCusto, tipoDespesa, planoConta,
		  tipoSaida, valor, desconto, juros, valorTotal,
		   formaPagamento, uploadComprovante, statusPagamento,
		    foiParcelado)

		    VALUES

			(   '".$notaFiscal."',
			    '".$fornecedor."',
			    '".$descricao."',
			    '".$statusCaixa."',
			    '".$tipoLancamento."',
			    '".$dataPagamento."',
			    '".$dataVencimento."',
			    '".$date."',
			    '".$centroCusto."',
			    '".$tipoDespesa."',
			    '".$planoConta."',
			    '".$tipoSaida."',
			    '".$valor."',
			    '".$desconto."',
			    '".$juros."',
			    '".$valorTotal."',
			    '".$formaPagamento."',
			    '".$uploadComprovante."',
			    '".$statusPagamento."',
			    '".$foiParcelado."'

			    )";


			$bGetKeyBack = True;

		}

		 RunQuery($sSQL);

	   if ( $_POST["PersonSubmit"] ) {
	   		Redirect ("lancamentos.php");
	    }



        require "Include/Header.php";

?>
<form method="post" action="AdicionarDespesa.php?PersonID=<?= $iPersonID ?>" name="AdicionarDespesa" >


	<div class="box box-info clearfix">
		<div class="box-header">


		</div><!-- /.box-header -->
		<div class="box-body">

	<div class="form-group">
	 	<div class="row">


			<div class="col-xs-3">
				<label for="notaFiscal"><?= gettext("Nota Fiscal/ Doc") ?></label>
				<input type="text" name="notaFiscal" id="notaFiscal"  class="form-control">
			</div>

			<div class="col-xs-3">
				<label for="fornecedor"><?= gettext("Fornecedor / Empresa") ?></label>
				<input type="text" name="fornecedor" id="fornecedor" value="<?= htmlentities(stripslashes($fornecedor),ENT_NOQUOTES, "UTF-8") ?>" class="form-control">
			</div>

			<div class="col-xs-3">
						<label for="descricao"><?= gettext("Descrição") ?></label>
						<input type="text" name="descricao" id="descricao" value="<?= htmlentities(stripslashes($descricao),ENT_NOQUOTES, "UTF-8") ?>" class="form-control">
			</div>

			<input type="hidden" name= "statusCaixa" value="0"></input>

			<input type="hidden" name= "tipoLancamento" value="Despesa"></input>

			<div class="col-xs-3">
					<label for="descricao"><?= gettext("Data Vencimento") ?></label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
			         </div>
			<input type="text" name="dataVencimento" class="form-control inputDatePicker" value="" maxlength="10" id="sel2" size="11" placeholder="YYYY-MM-DD">
			</div>
			</div>

	</div>
		</div>




			<div class="form-group">
	 	         <div class="row">

			<div class="col-xs-3">
				<label for="descricao"><?= gettext("Data Pagamento") ?></label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>


					<input type="text" name="dataPagamento" class="form-control inputDatePicker" value="" maxlength="10" id="sel2" size="11" placeholder="YYYY-MM-DD">
				</div>
			</div>

			<div class="col-xs-3">
				<label><?= gettext("Centro de custo") ?></label>
				<select name="centroCusto" class="form-control">
					<option value="1">Administração </option>
					<option value="1">Transporte </option>
					<option value="1">Manutenção</option>
					<option value="1">Missão </option>
					<option value="1">Curso </option>
				</select>
			</div>

			<div class="col-xs-3">

						<label><?= gettext("Tipo Despesa") ?></label>
						<select name="tipoDespesa" class="form-control">
							<option value="1">Conta Água</option>
							<option value="2">Conta de Luz</option>

						</select>
			</div>


				<div class="col-xs-3">
						<label><?= gettext("Centro de custo") ?></label>
						<select name="centroCusto" class="form-control">
							<option value="1">Administração </option>
							<option value="1">Transporte </option>
							<option value="1">Manutenção</option>
							<option value="1">Missão </option>
							<option value="1">Curso </option>
						</select>
					</div>


			</div>
			</div>

			<div class="form-group">
	 	         <div class="row">

					<div class="col-xs-3">
						<label><?= gettext("Plano Conta") ?></label>
						<select name="planoConta" class="form-control">
							<option value="1">Administração </option>
							<option value="1">Transporte </option>
							<option value="1">Manutenção</option>
							<option value="1">Missão </option>
							<option value="1">Curso </option>
						</select>
					</div>




					<div class="col-xs-3">
						<label><?= gettext("Tipo de Saída") ?></label>
						<select name="tipoSaida" class="form-control">
							<option value="1">Energia Elétrica </option>
							<option value="1">Conta de Agua</option>
							<option value="1">Fixa</option>
						</select>
					</div>


					<div class="col-xs-3">
						<label for="valor"><?= gettext("Valor") ?></label>
						<input type="valor" name="valor" id="valor" value="<?= htmlentities(stripslashes($valor),ENT_NOQUOTES, "UTF-8") ?>" class="form-control">
						<?php if ($valor) { ?><br><font color="red"><?php echo $valor ?></font><?php } ?>
					</div>

					<div class="col-xs-3">
						<label for="desconto"><?= gettext("Desconto") ?></label>
						<input type="text" name="desconto" id="desconto" value="<?= htmlentities(stripslashes($sLastName),ENT_NOQUOTES, "UTF-8") ?>" class="form-control">
						<?php if ($desconto) { ?><br><font color="red"><?php echo $desconto ?></font><?php } ?>
					</div>

					</div>
					</div>


					<div class="form-group">
				<div class="row">



					<div class="col-xs-3">
						<label for="juros"><?= gettext("Juros") ?></label>
						<input type="text" name="juros" id="juros" value="<?= htmlentities(stripslashes($juros),ENT_NOQUOTES, "UTF-8") ?>" class="form-control">
						<?php if ($juros) { ?><br><font color="red"><?php echo $juros ?></font><?php } ?>
					</div>

					<div class="col-xs-3">
						<label for="valorTotal"><?= gettext("Valor Total") ?></label>
						<input type="text" name="valorTotal" id="valorTotal" value="<?= htmlentities(stripslashes($valorTotal),ENT_NOQUOTES, "UTF-8") ?>" class="form-control">
						<?php if ($valorTotal) { ?><br><font color="red"><?php echo $valorTotal ?></font><?php } ?>
					</div>


				<div class="col-xs-3">
						<label><?= gettext("Forma Pagamento") ?></label>
						<select name="formaPagamento" class="form-control">
							<option value="1">Dinheiro</option>
							<option value="1">Cartão de Crédito</option>
							<option value="1">Boleto</option>
							<option value="1">Cheque</option>
							<option value="1">Depósito</option>

						</select>
				</div>

				<div class="col-xs-3">
						<label><?= gettext("Status Pagamento") ?></label>
						<select class="form-control" name= "statusPagamento" id="statusPagamento">

              <option value="Aberto" > Aberto</option>
               <option value="Fechado" >Fechado</option>


           </select>

				</div>

				</div>
				</div>

				<div class="form-group">
				<div class="row">


				  <div class="col-xs-4">
					  		<label><?= gettext("Comprovante ou Recibo (Imagem ou PDF) caso tenha") ?></label>
                             <input type="file" name = "uploadComprovante" id = "uploadComprovante"value="">
					  </div>


				</div>


					  <div class="col-xs-4">
						<div class="checkbox">
                              <label><input type="checkbox" id = "foiParcelado" name = "foiParcelado" value="">Foi Parcelado ?</label>
                         </div>
					  </div>



					 </div>
					 </div>



			<div class="pull-right"><br/>
				<input type="submit" class="btn btn-primary" value="<?= gettext("Salvar") ?>" name="PersonSubmit">
			</div>
		</div>
	</div>





</form>
<!-- InputMask -->
<script src="<?= $sRootPath ?>/skin/adminlte/plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
<script src="<?= $sRootPath ?>/skin/adminlte/plugins/input-mask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
<script src="<?= $sRootPath ?>/skin/adminlte/plugins/input-mask/jquery.inputmask.extensions.js" type="text/javascript"></script>
<script src="<?= $sRootPath ?>/skin/adminlte/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>

<script type="text/javascript">
	$(function() {
		$("[data-mask]").inputmask();
		$('.inputDatePicker').datepicker({format:'dd-mm-yyyy'});

	});
</script>

<?php require "Include/Footer.php" ?>