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
$sPageTitle = gettext("Caixa");

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
$rsSecurityGrp = RunQuery($sSQL);

while ($aRow = mysql_fetch_array($rsSecurityGrp))
{
	extract ($aRow);
	$aSecurityType[$lst_OptionID] = $lst_OptionName;
}




require "Include/Header.php";


//$id = $_GET['per_ID'];
$de      = $_GET['de'];
$ate     = $_GET['ate'];


//$stmt = $con->prepare("SELECT * FROM  adicionarDespesa WHERE dataCadastro = :id");

/*Lista de Despesa*/
$stmt = $con->prepare("SELECT * from Despesa
  where dataCadastro BETWEEN :de AND :ate and statusCaixa != '0'");
$stmt->bindParam('de', $de);
$stmt->bindParam('ate', $ate);
$stmt->execute();
$despesas = $stmt->fetchAll(\PDO::FETCH_ASSOC);



/*Lista de  Entrada*/
$stmt = $con->prepare("SELECT * from Entrada
  where dataCadastro BETWEEN :de AND :ate and statusCaixa = '0'");
$stmt->bindParam('de', $de);
$stmt->bindParam('ate', $ate);
$stmt->execute();
$entradas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

/*Lista de  Dízimo*/
$stmt = $con->prepare("SELECT * from  Dizimo
  where dataCadastro BETWEEN :de AND :ate and statusCaixa = '0'");
$stmt->bindParam('de', $de);
$stmt->bindParam('ate', $ate);
$stmt->execute();
$dizimos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

/*Lista de  Ofertas*/
$stmt = $con->prepare("SELECT * from Oferta
 where dataCadastro BETWEEN :de AND :ate and statusCaixa = '0'");
$stmt->bindParam('de', $de);
$stmt->bindParam('ate', $ate);
$stmt->execute();
$ofertas = $stmt->fetch(PDO::FETCH_ASSOC);



/*Somas*/


/*soma de  Despesa*/
$stmt = $con->prepare("SELECT SUM(valor) from Despesa
 where dataCadastro BETWEEN :de AND :ate and statusCaixa != '0'");
$stmt->bindParam('de', $de);
$stmt->bindParam('ate', $ate);
$stmt->execute();
$totalDespesas = $stmt->fetch(PDO::FETCH_ASSOC);

foreach ($totalDespesas as $totalDespesa ) {

}


/*Soma de  Entradas*/
$stmt = $con->prepare("SELECT SUM(valor) from  Entrada
 where dataCadastro BETWEEN :de AND :ate and statusCaixa = '0'");
$stmt->bindParam('de', $de);
$stmt->bindParam('ate', $ate);
$stmt->execute();
$totalEntras = $stmt->fetch(PDO::FETCH_ASSOC);

foreach ($totalEntras as $totalEntra ) {

}

/*Soma de  Dizimo*/
$stmt = $con->prepare("SELECT SUM(valor) from Dizimo
 where dataCadastro BETWEEN :de AND :ate and statusCaixa = '0'");
$stmt->bindParam('de', $de);
$stmt->bindParam('ate', $ate);
$stmt->execute();
$totalDizimos = $stmt->fetch(PDO::FETCH_ASSOC);

foreach ($totalDizimos as $totalDizimo ) {

}

/*Soma de  Ofertas*/
$stmt = $con->prepare("SELECT SUM(valor) from Oferta
 where dataCadastro BETWEEN :de AND :ate and statusCaixa = '0'");
$stmt->bindParam('de', $de);
$stmt->bindParam('ate', $ate);
$stmt->execute();
$totalOfertas = $stmt->fetch(PDO::FETCH_ASSOC);

foreach ($totalOfertas as $totalOferta ) {

}



$receita = $totalEntrada + $totalDizimo + $totalOferta;

$saldo = $receita - $totalDespesa;








?>


<form method="GET" role="form" action="Caixa.php" >



		<div class="form-group">
				<div class="row">
					<div class="col-xs-5">

					</div>


					<div class="col-xs-3">
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>


						<input type="text" name="de" class="form-control inputDatePicker" value="" maxlength="10" id="sel2" size="11" placeholder="YYYY-MM-DD">
					</div>
					</div>

					<div class="col-xs-3">
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>


						<input type="text" name="ate" class="form-control inputDatePicker" value="<?= $date ?>" maxlength="10" id="sel2" size="11" placeholder="YYYY-MM-DD">
					</div>
					</div>


					<button type="submit" class="btn btn-default">Filtrar</button>

				</div>
				</div>


  </form>

  <div class="container">
   <h2> Despesas </h2>
  <table class="table table-hover">
    <thead>
      <tr>
        <th>Receita</th>
        <th>Despesa</th>
        <th>Saldo</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <td><?= $receita?></td>
        <td><?= $totalDespesa?></td>
        <td><?= $saldo?></td>


      </tr>

    </tbody>
  </table>
</div>

<?php
//Is this the second pass?
if (isset($_POST["PersonSubmit"]) || isset($_POST["PersonSubmitAndAdd"]))
{
	//Get all the variables from the request object and assign them locally
      $de                              = FilterInput($_POST["de"]);
      $ate                             = FilterInput($_POST["ate"]);
      $totalDespesa             = FilterInput($_POST["totalDespesa"]);
      $totalEntrada              = FilterInput($_POST["totalEntrada"]);
	$totalDizimo               = FilterInput($_POST["totalDizimo"]);
      $totalOferta                = FilterInput($_POST["totalOferta"]);
      $Receita                      = FilterInput($_POST["receita"]);
      $Saldo                         = FilterInput($_POST["saldo"]);
      $statusCaixa               = FilterInput($_POST["statusCaixa"]);
      $dataFechamento       = date("Y-m-d h:i:sa");


			//Comando para persistir os dados no mysql
		$sSQL = "INSERT INTO Caixa (de, ate, totalDespesa, totalEntrada,
    totalDizimo, totalOferta, receita, saldo, statusCaixa, dataFechamento
   ) VALUES

			(

			    '".$de."',
			    '".$ate."',
			    '".$totalDespesa."',
                       '".$totalEntrada."',
                       '".$totalDizimo."',
                       '".$totalOferta."',
                       '".$Receita."',
                        '".$Saldo."',
                        '".$statusCaixa."',
			    '".$dataFechamento."')";

		$bGetKeyBack = True;

		}

		 RunQuery($sSQL);

		 if (isset($_POST["PersonSubmit"]))
		 {
		 	$sSQL = "UPDATE Despesa set statusCaixa = '1' where dataCadastro
		 BETWEEN '$de' and '$ate'";
		 $bGetKeyBack = True;
		 }

		 RunQuery($sSQL);



?>


<form method="post" action="caixa" name="AdicionarDespesa" >

	<input type="hidden" value="<?= $_GET['de'] ?>" name="de"></input>
	<input type="hidden" value="<?= $_GET['ate'] ?>" name="ate"></input>
       <input type="hidden" value="<?= $totalDespesa ?>" name="totalDespesa"></input>
      <input type="hidden" value="<?= $totalEntrada ?>" name="totalEntrada"></input>
     <input type="hidden" value="<?= $totalDizimo ?>" name="totalDizimo"></input>
	<input type="hidden" value="<?= $totalOferta ?>" name="totalOferta"></input>
     <input type="hidden" value="<?= $receita?>" name="receita"></input>
     <input type="hidden" value="<?= $saldo?>" name="saldo"></input>
     <input type="hidden" value="Fechado" name="statusCaixa"></input>



	<div class="text-right">
		<input type="submit" class="btn btn-primary"
      name="PersonSubmit" value = "Fechar Caixa">

	</div>


</form>


<div class="container">
   <h2> Caixa</h2>
   <div  class="col-md-10">
  <table class="table table-hover">

    <thead>
      <tr>
        <th>Membro/Fornecedor/Descrição</th>
        <th>Tipo Lançamento</th>
        <th>Status Pagamento</th>
         <th>Valor</th>
         <th>Data Pagamento</th>
         <th>Vencimento</th>


      </tr>
    </thead>

    <tbody>

    <?php foreach ($despesas as $despesa): ?>
      <tr>
        <td><?php echo $despesa['fornecedor'];?></td>
        <td><?php echo $despesa['tipoLancamento'];?></td>
        <td><?php echo $despesa['statusPagamento'];?></td>
         <td><?php echo $despesa['valor'];?></td>
         <td><?php echo $despesa['dataPagamento'];?></td>
        <td><?php echo $despesa['dataVencimento'];?></td>





      </tr>
     <?php endforeach ?>

    </tbody>

    <tbody>

    <?php foreach ($entradas as $entrada): ?>
      <tr>

        <td><?php echo $entrada['fornecedor'];?></td>
        <td><?php echo $entrada['tipoDespesa'];?></td>
        <td><?php echo $entrada['statusPagamento'];?></td>
        <td><?php echo $entrada['dataVencimento'];?></td>
        <td><?php echo $entrada['valor'];?></td>

      </tr>
     <?php endforeach ?>

    </tbody>

    <tbody>

    <?php foreach ($dizimos as $dizimo): ?>
      <tr>

        <td><?php echo $dizimo['fornecedor'];?></td>
        <td><?php echo $dizimo['tipoDespesa'];?></td>
        <td><?php echo $dizimo['statusPagamento'];?></td>
        <td><?php echo $dizimo['dataVencimento'];?></td>
        <td><?php echo $dizimo['valor'];?></td>

      </tr>
     <?php endforeach ?>

    </tbody>

    <tbody>

    <?php foreach ($ofertas as $oferta): ?>
      <tr>

        <td><?php echo $oferta['fornecedor'];?></td>
        <td><?php echo $oferta['tipoDespesa'];?></td>
        <td><?php echo $oferta['statusPagamento'];?></td>
        <td><?php echo $oferta['dataVencimento'];?></td>
        <td><?php echo $oferta['valor'];?></td>

      </tr>
     <?php endforeach ?>

    </tbody>

  </table>
  </div>
</div>




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





