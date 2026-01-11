import { Component } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';
import { ActivatedRoute } from '@angular/router';
import { filter, map, switchMap, tap } from 'rxjs';
import { Offcanvas } from 'bootstrap';

@Component({
  selector: 'app-catalog-edit-rdf',
  standalone: false,
  templateUrl: './catalog-edit-rdf.component.html',
  styleUrls: ['./catalog-edit-rdf.component.scss'], // ✅ corrigido
})
export class CatalogEditRdfComponent {
  id: string = '';
  data: any;
  editData: any;

  constructor(
    private route: ActivatedRoute,
    private findService: FindService
  ) {}

  sections = [
    { key: 'Work', title: 'Trabalho - Work' },
    { key: 'Expression', title: 'Expressão - Expression' },
    { key: 'Manifestation', title: 'Manifestação - Manifestation' },
    { key: 'Editora', title: 'Editora - Publisher' },
  ];

  private offcanvasInstance?: Offcanvas;

  action(event: any) {
    if (event === 'saved') {
      this.offcanvasInstance?.hide();
    }
  }

  sonEvent(event: any) {
    this.editData = event[0];
    const element = document.getElementById('editorRdfOffcanvas');
    if (!element) return;
    this.offcanvasInstance = Offcanvas.getOrCreateInstance(element);
    this.offcanvasInstance.show();
  }

  newData(item: any) {
    this.editData = item;
    const element = document.getElementById('editorRdfOffcanvas');
    if (!element) return;
    this.offcanvasInstance = Offcanvas.getOrCreateInstance(element);
    this.offcanvasInstance.show();
  }

  ngOnInit() {
    this.route.paramMap
      .pipe(
        map((params) => Number(params.get('id'))),
        filter((id) => !Number.isNaN(id)), // garante ID válido
        tap((id) => (this.id = String(id))),
        switchMap((id) =>
          this.findService.api_post('form/edit/' + this.id, {
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
