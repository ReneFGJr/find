import { ActivatedRoute, ParamMap } from '@angular/router';
import { Component } from '@angular/core';
import { FindService } from '../../service/find.service';

@Component({
  selector: 'app-rdf-form-edit',
  standalone: false,
  templateUrl: './rdf-form-edit.component.html',
  styleUrl: './rdf-form-edit.component.scss',
})
export class RdfFormEditComponent {
  type: string | null = null;
  data: Array<any> | any;

  subgrupos: string[] = [
    '',
    'TITLE',
    'AUTOR',
    'EDITOR',
    'CLASSIFICATION',
    'SUBJECT',
    'SUBTITLE',
    'EDITION',
    'SERIE',
    'NOTE',
    'LANGUAGE',
    'OTHER',
  ];
  selectedSubgrupo: string = 'TITLE'; // valor inicial

  selectedWT: any[] = [];
  selectedWO: any[] = [];

  constructor(
    private route: ActivatedRoute,
    private findService: FindService
  ) {}

  ngOnInit(): void {
    this.route.paramMap.subscribe((params: ParamMap) => {
      this.type = params.get('type');
      this.selectedSubgrupo = params.get('group') || 'TITLE'; // valor inicial
      console.log('Parametro type:', this.type);

      this.findService
        .api_post('form/property', {
          type: this.type,
          subgroup: this.selectedSubgrupo,
        })
        .subscribe((res) => {
          this.data = res;
          console.log('Dados recebidos:', this.data);
          this.selectedWT = this.data ? this.data.WT || [] : [];
          this.selectedWO = this.data ? this.data.WO || [] : [];
        });
    });
  }

  /** move de WO para WT */
  addToWT() {
    this.data.WO = this.data.WO.filter((item: any) => {
      if (this.selectedWO.includes(item.id_c)) {
        this.data.WT.push(item);
        return false;
      }
      return true;
    });
    this.selectedWO = [];
  }

  /** move de WT para WO */
  removeFromWT() {
    this.data.WT = this.data.WT.filter((item: any) => {
      if (this.selectedWT.includes(item.id_c)) {
        this.data.WO.push(item);
        return false;
      }
      return true;
    });
    this.selectedWT = [];
  }

  salvar() {
    const payload = {
      type: this.type,
      subgroup: this.selectedSubgrupo,
      // pega apenas os id_c dos itens de WO
      WO: this.data.WO.map((item: any) => item.id_c),
    };

    console.log('Payload para salvar:', payload);

    this.findService.api_post('form/property_save', payload).subscribe({
      next: (res) => {
        console.log('Salvo com sucesso:', res);
        alert('Propriedades salvas com sucesso!');
      },
      error: (err) => {
        console.error('Erro ao salvar:', err);
        alert('Erro ao salvar propriedades!');
      },
    });
  }
}
