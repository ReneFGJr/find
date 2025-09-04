import { Component } from '@angular/core';

@Component({
  selector: 'app-configura-library',
  standalone: false,
  templateUrl: './configura-library.component.html',
  styleUrl: './configura-library.component.scss',
})
export class ConfiguraLibraryComponent {
  data: any;
  id: number = 0;
  termID: number = 0;
  url: string = '';
  editMode: boolean = false;

  sections = [
    { id: 'Title', title: 'Biblioteca' },
    { id: 'Descript', title: 'Descrição' },
    { id: 'Methodology', title: 'Metodologia' },
    { id: 'Audience', title: 'Público Alvo' },
    { id: 'Visibility', title: 'Visibilidade' },
    { id: 'Themes', title: 'Thema e Incones' },
    { id: 'Members', title: 'Membros' },
  ];

  selectedSection = 'Title';

  selectSection(id: string) {
    this.selectedSection = id;
  }

  update()
    {

    }
}
