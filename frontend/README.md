# Coorp Travel Management - Frontend

Este projeto é o frontend do sistema Coorp Travel Management, responsável pela interface de usuário para gerenciamento de viagens corporativas.

## Tecnologias Utilizadas

- [Vue 3](https://vuejs.org/) + [Vite](https://vitejs.dev/)
- [TypeScript](https://www.typescriptlang.org/)
- [Pinia](https://pinia.vuejs.org/) para gerenciamento de estado
- [Vitest](https://vitest.dev/) para testes
- [ESLint](https://eslint.org/) e [Prettier](https://prettier.io/) para padronização de código

## Estrutura de Pastas

- `src/` - Código-fonte principal
  - `api/` - Serviços de integração com backend
  - `components/` - Componentes reutilizáveis
  - `composables/` - Composables Vue (hooks)
  - `pages/` - Páginas da aplicação
  - `router/` - Configuração de rotas
  - `stores/` - Gerenciamento de estado (Pinia)
  - `utils/` - Utilitários
- `public/` - Arquivos públicos e estáticos
- `docker/` - Configuração para container Docker

## Instalação e Execução

1. **Instale as dependências:**

```sh
pnpm install
```

2. **Crie o arquivo de variáveis de ambiente:**

Copie `.env.example` para `.env` e ajuste conforme necessário.

3. **Execute o projeto em modo desenvolvimento:**

```sh
pnpm dev
```

4. **Acesse em:**

[http://localhost:5173](http://localhost:5173)

## Scripts Úteis

- `pnpm dev` — Inicia o servidor de desenvolvimento
- `pnpm build` — Gera build de produção
- `pnpm preview` — Visualiza build de produção localmente
- `pnpm test` — Executa os testes unitários
- `pnpm lint` — Executa o linter

## Testes

Os testes estão localizados em `src/__tests__/`. Utilize `pnpm test` para rodar os testes.

## Docker

Para rodar o frontend em container Docker:

```sh
docker build -t coorp-frontend -f docker/frontend/Dockerfile .
docker run -p 5173:80 coorp-frontend
```

## Contribuição

1. Faça um fork do projeto
2. Crie uma branch: `git checkout -b minha-feature`
3. Commit suas alterações: `git commit -m 'feat: minha nova feature'`
4. Push para o branch: `git push origin minha-feature`
5. Abra um Pull Request

## Licença

Este projeto está sob a licença MIT.
