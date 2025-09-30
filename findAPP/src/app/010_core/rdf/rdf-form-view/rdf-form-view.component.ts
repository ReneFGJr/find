import { FindService } from './../../service/find.service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-rdf-form-view',
  standalone: false,
  templateUrl: './rdf-form-view.component.html',
  styleUrl: './rdf-form-view.component.scss',
})
export class RdfFormViewComponent {
  data: any; // recebe o JSON como input

  constructor(private FindService: FindService) {}

  ngOnInit() {
    this.FindService.api_post('form/formByLibrary', {}).subscribe({
      next: (res) => {
        this.data = res;
      },
    });
  }

  frbrMap: any = {
    W: 'WORK',
    E: 'EXPRESSION',
    M: 'MANIFESTATION',
  };

  get frbrKeys(): string[] {
    return ['W', 'E', 'M'];
  }

  moveItem(property: any, direction: 'up' | 'down') {
    let dt = {
      id: property.id_form,
      direction: direction,
    }
    console.log(dt);

    this.FindService.api_post('form/moveProperty', dt).subscribe({
      next: (res) => {
        this.data = res;
      },
    });
  }

  /** organiza os dados por FRBR > grupo */
  getDataByFrbr(frbr: string) {
    const result: Record<string, any[]> = {}; // 👈 força value a ser array
    if (!this.data) return result;

    Object.keys(this.data)
      .filter((k) => !['d1', 'd2', 'd3'].includes(k))
      .forEach((group) => {
        const itens = this.data[group].filter((i: any) => i.frbr === frbr);
        if (itens.length > 0) {
          result[group] = itens;
        }
      });

    return result;
  }
}
