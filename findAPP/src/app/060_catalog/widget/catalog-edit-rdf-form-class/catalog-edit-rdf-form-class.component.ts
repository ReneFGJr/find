import { Component, EventEmitter, Input, Output, SimpleChanges } from '@angular/core';
import { FindService } from '../../../010_core/service/find.service';
import { FormControl, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-catalog-edit-rdf-form-class',
  standalone: false,
  templateUrl: './catalog-edit-rdf-form-class.component.html',
  styleUrl: './catalog-edit-rdf-form-class.component.scss'
})
export class CatalogEditRdfFormClassComponent {
  @Input() data: any
  @Input() ID: any
  @Output() action = new EventEmitter<any>();

  options: any = [];
  form = new FormGroup({
    classKey: new FormControl(''),
    concept: new FormControl(''),
    selectID: new FormControl('', Validators.required),
    conceptID: new FormControl(''),
  });

  constructor(
    private findService: FindService
  ) { }

  private lastSearchTerm = '';

  onConceptChange(): void {
    const value = this.form.get('concept')?.value;

    this.form.patchValue({
      selectID: value
    });
  }


  RDFsave(): void {
  }

  RDFEditSearch(): void {

    const term: string = (this.form.value.classKey || '').trim();

    if (term.length < 3 || term === this.lastSearchTerm) {
      return;
    }

    this.lastSearchTerm = term;

    const dt = {
      formID: this.data.id_form,
      recordId: this.data.c_class,
      searchTerm: term
    };

    this.findService
      .api_post('form/search', dt)
      .subscribe({
        next: (response: any) => {
          console.log('🔎 Termos encontrados:', response);
          this.options = Array.isArray(response?.options)
            ? response.options
            : [];
        },
        error: (err) => {
          console.error('❌ Erro na busca RDF', err);
          this.options = [];
        }
      });
  }


  RDFEditStart(): void {

    if (!this.data) {
      console.warn('RDFEditStart chamado sem data');
      return;
    }

    const dt = {
      formId: this.data.id_form,
      recordId: this.data.c_class,
      classKey: this.data.classKey
    };

    this.form.patchValue({
      conceptID: this.ID
    });

    console.log('➡️ Enviando para API:', dt);

    this.findService
      .api_post('form/concept', dt)
      .subscribe({
        next: (response: any) => {

          console.log('⬅️ Resposta da API:', response);

          // 🔹 Preenche opções do select
          if (Array.isArray(response?.options)) {
            this.options = response.options;
          } else {
            this.options = [];
          }

          // 🔹 Sincroniza formulário (se vier da API)
          this.form.patchValue({
            classKey: response?.classKey ?? this.data.classKey ?? '',
            concept: response?.concept ?? ''
          });
        },

        error: (err) => {
          console.error('❌ Erro ao buscar conceitos RDF', err);
          this.options = [];
        }
      });
  }


  ngOnChanges(changes: SimpleChanges): void {
    if (changes['data'] && changes['data'].currentValue) {
      this.RDFEditStart();
    }
  }
}
