export interface User {
  id_us: any;
  id_user: number; // id do usuário
  us_nome: string;
  us_email: string;
  us_login: string;
  us_last: string; // ex: "20170715" ou "0"
  us_image: string; // pode ficar vazio
  us_genero: string; // "M", "F" ou string vazia
  us_cadastro: string; // ex: "20140706" ou "0"
  ul_library: string; // biblioteca do usuário
  // (caso queira manipular datas, depois fazemos parsing para Date)
}
