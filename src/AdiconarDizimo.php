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
$sPageTitle = gettext("Adicionar Dízimo");

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
	$membro                      = FilterInput($_POST["membro"]);
	$planoConta                  = FilterInput($_POST["planoConta"]);
	$dataPagamento               = FilterInput($_POST["dataPagamento"]);
	$statusCaixa                 = FilterInput($_POST["statusCaixa"]);
	$tipoLancamento                 = FilterInput($_POST["tipoLancamento"]);
	$obs                         = FilterInput($_POST["obs"]);
	$valor                       = FilterInput($_POST["valor"]);
  	$formaPag                    = FilterInput($_POST["formaPag"]);





			//Comando para persistir os dados no mysql
		$sSQL = "INSERT INTO Dizimo (
		membro, planoConta, dataPagamento, dataCadastro,
		 statusCaixa, obs, valor, formaPag)
		 VALUES

			('".$membro."',
			 '".$planoConta."',
			 '".$dataPagamento."',
			 '".$date."',
			 '".$statusCaixa."',
			 '".$obs."',
			 '".$valor."',
			 '".$formaPag."')";


			$bGetKeyBack = True;

		}

		 RunQuery($sSQL);

	   if ( $_POST["PersonSubmit"] ) {
	   		Redirect ("lancamentos.php");
	    }



        require "Include/Header.php";



?>
<form method="post" action="AdiconarDizimo.php?PersonID=<?= $iPersonID ?>" name="AdicionarDizimo" >

	<div class="box box-info clearfix">
		<div class="box-header">


		</div><!-- /.box-header -->
		<div class="box-body">
			<div class="form-group">
				<div class="row">


					<div class="col-xs-3">
						<label for="membro"><?= gettext("Membro") ?></label>
						<input type="text" name="membro" id="membro" value="<?= htmlentities(stripslashes($sTitle),ENT_NOQUOTES, "UTF-8") ?>" class="form-control">
					</div>

					<div class="col-xs-3">
						<label><?= gettext("Plano Conta") ?></label>
						<select name="planoConta"  class="form-control">
							<option value="1">Administração </option>
							<option value="1">Transporte </option>
							<option value="1">Manutenção</option>
							<option value="1">Missão </option>
							<option value="1">Curso </option>
						</select>
					</div>

			<div class="col-xs-3">
				<label for="descricao"><?= gettext("Data Pagamento") ?></label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
		         </div>
				     <input type="text" name="dataPagamento" class="form-control inputDatePicker" value="" maxlength="10" id="sel2" size="11" placeholder="YYYY-MM-DD">
				</div>
			</div>

				<input type="hidden" name= "statusCaixa" value="0"></input>

				<input type="hidden" name= "tipoLancamento" value="Dízimo"></input>

					<div class="col-xs-3">
						<label for="obs"><?= gettext("Obs") ?></label>
						<input type="text" name="obs"  value="<?= htmlentities(stripslashes($sTitle),ENT_NOQUOTES, "UTF-8") ?>" class="form-control">
					</div>

				</div>
				</div>

				<div class="form-group">
				<div class="row">




					<div class="col-xs-3">
						<label for="valor"><?= gettext("Valor") ?></label>
						<input type="text" name="valor"  value="<?= htmlentities(stripslashes($sTitle),ENT_NOQUOTES, "UTF-8") ?>" class="form-control">
					</div>




					<div class="col-xs-3">
						<label><?= gettext("Forma Pagamento") ?></label>
						<select name="formaPag" class="form-control">
							<option value="1">Energia Elétrica </option>
							<option value="1">Conta de Agua</option>
							<option value="1">Fixa</option>
						</select>
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
		$('.inputDatePicker').datepicker({format:'yyyy-mm-dd'});

	});
</script>

<?php require "Include/Footer.php" ?>
