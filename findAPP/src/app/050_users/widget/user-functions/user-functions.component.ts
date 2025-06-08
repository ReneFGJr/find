import { Component } from '@angular/core';

@Component({
  selector: 'app-user-functions',
  standalone: false,
  templateUrl: './user-functions.component.html',
  styleUrl: './user-functions.component.scss',
})
export class UserFunctionsComponent {
  functions: Array<{ name: string; description: string; icon: string }> = [
    {
      name: 'Leitor',
      description: 'Usuário da Biblioteca',
      icon: 'assets/icons/person_reader_01.png',
    },
    {
      name: 'Administrador',
      description: 'Administrador do sistema',
      icon: 'assets/icons/person_adm_w.png',
    },
    {
      name: 'Bibliotecário',
      description: 'Bibliotecário',
      icon: '/assets/icons/person_librarian_w.png',
    },
    {
      name: 'Bibliotecário Júnior',
      description: 'Bibliotecário Júnior',
      icon: '/assets/icons/person_librarian_jr.png',
    },
  ];
}
