# Contribuindo

## Fluxo

1. Crie uma branch curta a partir de `main`.
2. Faça alterações pequenas e coesas.
3. Execute lint e valide o fluxo afetado.
4. Abra um pull request com contexto, evidências e riscos.

## Branches

Use nomes simples, por exemplo:

- `feat/lembretes-consulta`
- `fix/conflito-agenda`
- `refactor/modulo-pacientes`
- `docs/instalacao`

## Commits

As mensagens seguem Conventional Commits, em português e no imperativo:

- `feat: adiciona filtro por médico`
- `fix: corrige conflito de horário`
- `refactor: separa módulo de pacientes`
- `docs: documenta instalação local`
- `chore: organiza arquivos públicos`
- `style: ajusta layout da agenda`
- `test: cobre validação de CPF`
- `perf: otimiza consulta mensal`
- `revert: restaura fluxo de autenticação`

Evite commits genéricos como “ajustes”, “update” ou “código pronto”. Um commit deve explicar uma única intenção e permanecer fácil de revisar ou reverter.

## Padrões de código

- PHP 8.1+, `declare(strict_types=1)` e PSR-12;
- classes sob o namespace `Clinica\\`;
- SQL somente em repositories e sempre parametrizado;
- validação no servidor antes de persistir;
- escape com `e()` em toda saída dinâmica;
- POST + CSRF para qualquer mudança de estado;
- nenhuma credencial, dado pessoal real ou arquivo `.env` no Git.

## Checklist do pull request

- [ ] comportamento validado localmente;
- [ ] nenhuma credencial ou dado sensível incluído;
- [ ] schema/migração documentado quando necessário;
- [ ] mensagens de erro não expõem detalhes internos;
- [ ] README ou documentação atualizados;
- [ ] interface utilizável por teclado e em telas menores.
