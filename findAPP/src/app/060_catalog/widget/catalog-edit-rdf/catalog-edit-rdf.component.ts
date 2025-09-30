import { Component } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';
import { ActivatedRoute } from '@angular/router';
import { filter, map, switchMap, tap } from 'rxjs';

@Component({
  selector: 'app-catalog-edit-rdf',
  standalone: false,
  templateUrl: './catalog-edit-rdf.component.html',
  styleUrls: ['./catalog-edit-rdf.component.scss'], // ✅ corrigido
})
export class CatalogEditRdfComponent {
  id: string = '';
  data: any;


  constructor(
    private route: ActivatedRoute,
    private findService: FindService
  ) {}

  sections = [
    { key: 'Work', title: 'Trabalho - Work' },
    { key: 'Expression', title: 'Expressão - Expression' },
    { key: 'Manifestation', title: 'Manifestação - Manifestation' },
  ];

  ngOnInit() {
    this.route.paramMap
      .pipe(
        map((params) => Number(params.get('id'))),
        filter((id) => !Number.isNaN(id)), // garante ID válido
        tap((id) => (this.id = String(id))),
        switchMap((id) =>
          this.findService.api_post('form/property', {
            type: this.id,
            model: 'FIND',
          })
        )
      ) // ✅ retorna observable
      .subscribe((res) => {
        this.data = res;
      });
  }


}
