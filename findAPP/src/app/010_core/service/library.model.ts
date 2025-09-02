// src/app/library/library.model.ts
export interface Library {
  id_l?: number; // auto
  l_name: string; // text (obrigatório)
  l_code: string; // char(15)
  l_id: number; // int
  l_logo: string; // char(80) - caminho/URL
  l_about: string; // text
  l_visible: number; // 0/1
  l_net: number; // 0/1
}
