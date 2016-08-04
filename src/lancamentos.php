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
$sPageTitle = gettext("Lançamentos");

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


// Get the list of custom person fields
$sSQL = "SELECT person_custom_master.* FROM person_custom_master ORDER BY custom_Order";
$rsCustomFields = RunQuery($sSQL);
$numCustomFields = mysql_num_rows($rsCustomFields);

require "Include/Header.php";


$de                                   = $_GET['de'];
$ate                                  = $_GET['ate'];
$statusPagamento           = $_GET['situacao'];
$tipoLancamento             = $_GET['tipo'];


/*Lista de Despesa*/
$stmt = $con->prepare("SELECT * from Despesa
  where dataCadastro BETWEEN :de AND :ate and statusPagamento = :statusPagamento and tipoLancamento = :tipoLancamento");
$stmt->bindParam('de', $de);
$stmt->bindParam('ate', $ate);
$stmt->bindParam('statusPagamento', $statusPagamento);
$stmt->bindParam('tipoLancamento', $tipoLancamento);
$stmt->execute();
$despesas = $stmt->fetchAll(\PDO::FETCH_ASSOC);


//$id = $_GET['per_ID'];
$stmt = $con->prepare("select * from Entrada");
//$stmt->bindParam(":id", $id);
$stmt->execute();
$entradas = $stmt->fetchAll(\PDO::FETCH_ASSOC);


//$id = $_GET['per_ID'];
$stmt = $con->prepare("select * from Dizimo");
//$stmt->bindParam(":id", $id);
$stmt->execute();
$dizimos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

//$id = $_GET['per_ID'];
$stmt = $con->prepare("select * from Oferta");
//$stmt->bindParam(":id", $id);
$stmt->execute();
$ofertas = $stmt->fetchAll(\PDO::FETCH_ASSOC);


?>



	<form class="form-inline" role="form" action="lancamentos.php" name="cpf">
   <div class="container">

  <div class="col-md-1"></div>
     <label for="cpf">De:</label>
      <input type="cpf" class="form-control"  name = "de" >

       <label for="cpf">Ate:</label>
      <input type="cpf" class="form-control"  name = "ate" >

       <label for="cpf">Situação</label>
      <input type="cpf" class="form-control"  name = "situacao" >
<br>
       <label for="cpf">Tipo Lançamento:</label>
      <input type="cpf" class="form-control"  name = "tipo" >


    <button type="submit" class="btn btn-default">Pesquisar</button>


    </div>

</form>




<div class="container">
   <h2> Despesas </h2>
  <table class="table table-hover">
    <thead>
      <tr>
        <th>Código</th>
        <th>Membro/Fornecedor/Descrição</th>
        <th>Plano de Contas</th>
         <th>Status</th>
         <th>Vencimento</th>
         <th>Valor</th>
         <th>Ações</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($despesas as $despesa): ?>
      <tr>
        <td><?php echo $despesa['idAddDespesa'];?></td>
        <td><?php echo $despesa['fornecedor'];?></td>
        <td><?php echo $despesa['tipoDespesa'];?></td>
        <td><?php echo $despesa['statusPagamento'];?></td>
        <td><?php echo $despesa['dataVencimento'];?></td>
        <td><?php echo $despesa['valor'];?></td>
        <td><a href="#">Crud</a></td>
      </tr>
     <?php endforeach ?>
    </tbody>

     <tbody>
    <?php foreach ($entradas as $entrada): ?>
      <tr>
        <td><?php echo $entrada['idAddEntrada'];?></td>
        <td><?php echo $entrada['membro'];?></td>
        <td><?php echo $entrada['planoConta'];?></td>
        <td><?php echo $entrada['status'];?></td>
        <td><?php echo $entrada['dataVencimento'];?></td>
        <td><?php echo $entrada['valor'];?></td>
        <td><a href="#">Crud</a></td>
      </tr>
     <?php endforeach ?>
    </tbody>

     <tbody>
    <?php foreach ($dizimos as $dizimo): ?>
      <tr>
        <td><?php echo $dizimo['idAddDizimo'];?></td>
        <td><?php echo $dizimo['membro'];?></td>
        <td><?php echo $dizimo['planoConta'];?></td>
        <td><?php echo $dizimo['status'];?></td>
        <td><?php echo $dizimo['dataVencimento'];?></td>
        <td><?php echo $dizimo['valor'];?></td>
        <td><a href="#">Crud</a></td>
      </tr>
     <?php endforeach ?>
    </tbody>

    <tbody>
    <?php foreach ($ofertas as $oferta): ?>
      <tr>
        <td><?php echo $oferta['idAddOferta'];?></td>
        <td><?php echo $oferta['membro'];?></td>
        <td><?php echo $oferta['planoConta'];?></td>
        <td><?php echo $oferta['status'];?></td>
        <td><?php echo $oferta['dataVencimento'];?></td>
        <td><?php echo $oferta['valor'];?></td>
        <td><a href="#">Crud</a></td>
      </tr>
     <?php endforeach ?>
    </tbody>


  </table>
</div>








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
