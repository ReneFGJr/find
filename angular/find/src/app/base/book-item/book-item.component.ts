import { Component } from '@angular/core';

@Component({
  selector: 'app-book-item',
  templateUrl: './book-item.component.html',
  styleUrls: ['./book-item.component.scss']
})

export class BookItemComponent {
  books = <any>[
    { id_bk: 1, bk_isbn: "97400000000", bk_title: "Título da obra", bk_lang: 'pt-BR', bk_cover: 'https://www.ufrgs.br/find/_covers/image/9786599234415.jpg'},
    { id_bk: 2, bk_isbn: "97400000000", bk_title: "Título da obra", bk_lang: 'pt-BR', bk_cover: 'https://www.ufrgs.br/find/_covers/image/9786599234415.jpg' },
    { id_bk: 3, bk_isbn: "97400000000", bk_title: "Título da obra", bk_lang: 'pt-BR', bk_cover: 'https://www.ufrgs.br/find/_covers/image/9786599234415.jpg' },
    { id_bk: 4, bk_isbn: "97400000000", bk_title: "Título da obra", bk_lang: 'pt-BR', bk_cover: 'https://www.ufrgs.br/find/_covers/image/9786599234415.jpg' },
    { id_bk: 5, bk_isbn: "97400000000", bk_title: "Título da obra", bk_lang: 'pt-BR', bk_cover: 'https://www.ufrgs.br/find/_covers/image/9786599234415.jpg' },
    { id_bk: 5, bk_isbn: "97400000000", bk_title: "Título da obra", bk_lang: 'pt-BR', bk_cover: 'https://www.ufrgs.br/find/_covers/image/9786599234415.jpg' },
    { id_bk: 5, bk_isbn: "97400000000", bk_title: "Título da obra", bk_lang: 'pt-BR', bk_cover: 'https://www.ufrgs.br/find/_covers/image/9786599234415.jpg' },
    { id_bk: 5, bk_isbn: "97400000000", bk_title: "Título da obra", bk_lang: 'pt-BR', bk_cover: 'https://www.ufrgs.br/find/_covers/image/9786599234415.jpg' },
    { id_bk: 5, bk_isbn: "97400000000", bk_title: "Título da obra", bk_lang: 'pt-BR', bk_cover: 'https://www.ufrgs.br/find/_covers/image/9786599234415.jpg' },
    { id_bk: 5, bk_isbn: "97400000000", bk_title: "Título da obra", bk_lang: 'pt-BR', bk_cover: 'https://www.ufrgs.br/find/_covers/image/9786599234415.jpg' },
    { id_bk: 5, bk_isbn: "97400000000", bk_title: "Título da obra", bk_lang: 'pt-BR', bk_cover: 'https://www.ufrgs.br/find/_covers/image/9786599234415.jpg' },
    { id_bk: 5, bk_isbn: "97400000000", bk_title: "Título da obra", bk_lang: 'pt-BR', bk_cover: 'https://www.ufrgs.br/find/_covers/image/9786599234415.jpg' },
  ];
}
