<?php
	switch ($_REQUEST["acao"]) {
		case 'cadastrar':
			$nome = $_POST['nome_medico'];
			$crm = $_POST['crm_medico'];
			$especialidade = $_POST['especialidade_medico'];

			$sql = "INSERT INTO medico (
						nome_medico,
						crm_medico,
						especialidade_medico)
					VALUES (
						'{$nome}',
						'{$crm}',
						'{$especialidade}')";

			$res = $conn->query($sql);

			if($res==true){
				print "<script>alert('Cadastrou com sucesso!');</script>";
				print "<script>location.href='?page=listar-medico';</script>";
			}else{
				print "<script>alert('Não foi possível');</script>";
				print "<script>location.href='?page=listar-medico';</script>";
			}
			break;
		
		case 'editar':
			$sql = "UPDATE medico SET
						nome_medico='".$_POST['nome_medico']."',
						crm_medico='".$_POST['crm_medico']."',
						especialidade_medico='".$_POST['especialidade_medico']."'
					WHERE
						id_medico = ".$_POST['id_medico'];

			$res = $conn->query($sql);

			if($res==true){
				print "<script>alert('Editou com sucesso!');</script>";
				print "<script>location.href='?page=listar-medico';</script>";
			}else{
				print "<script>alert('Não foi possível');</script>";
				print "<script>location.href='?page=listar-medico';</script>";
			}
			break;

		case 'excluir':
			$sql = "DELETE FROM medico WHERE id_medico=".$_REQUEST["id_medico"];

			$res = $conn->query($sql);

			if($res==true){
				print "<script>alert('Excluiu com sucesso!');</script>";
				print "<script>location.href='?page=listar-medico';</script>";
			}else{
				print "<script>alert('Não foi possível');</script>";
				print "<script>location.href='?page=listar-medico';</script>";
			}
			break;
	}