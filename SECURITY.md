# Política de segurança

## Versões suportadas

Enquanto o projeto não possui releases estáveis, somente a versão mais recente da branch principal recebe correções de segurança.

## Reporte responsável

Não abra uma issue pública contendo payloads, credenciais, dados de pacientes ou detalhes que facilitem exploração. Envie um relato privado ao proprietário do repositório pelo recurso **Security > Report a vulnerability** do GitHub, quando habilitado.

Inclua:

- componente e versão afetados;
- impacto observado;
- passos mínimos de reprodução em ambiente controlado;
- pré-condições necessárias;
- sugestão de correção, se disponível.

## Dados sensíveis

Nunca use dados pessoais ou clínicos reais em desenvolvimento, demonstrações, issues ou pull requests. Utilize valores sintéticos. Em produção, TLS, controle de acesso, backups cifrados, auditoria e política de retenção são responsabilidades obrigatórias da implantação e da organização operadora.

## Credenciais

- `.env` não deve ser versionado;
- a aplicação não deve usar `root` do MySQL;
- senhas de usuários são armazenadas com `password_hash`;
- segredos expostos devem ser revogados, não apenas removidos do histórico atual.
