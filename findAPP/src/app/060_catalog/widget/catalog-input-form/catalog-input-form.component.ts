import { Component, inject, Input, signal } from '@angular/core';
import { FormArray, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { FindService } from '../../../010_core/service/find.service';

@Component({
  selector: 'app-catalog-input-form',
  standalone: false,
  templateUrl: './catalog-input-form.component.html',
  styleUrl: './catalog-input-form.component.scss',
})
export class CatalogInputFormComponent {
  @Input() editMode: boolean = true;
  searchForm: FormGroup;
  data: Array<any> | any;
  itens: Array<any> | any;
  message: string = '';
  type: string = '';

  constructor(private bookService: FindService) {
    this.searchForm = new FormGroup({
      searchQuery: new FormControl('9786589167501', [Validators.required]),
    });
  }

  onItemResponse(id: string) {
    /* Inserir novo Exemplar */
    let dt = {isbn: id}
    this.searchForm.reset();
    this.bookService
      .api_post('catalog/itemAdd', dt)
      .subscribe((res) => {
        this.data = res;
        this.itens = [];
        this.message = "Exemplar adicionado com sucesso!";
        this.message = this.message + '<br>ISBN: ' + id;
        this.message = this.message + '<br><a href="/catalog/item/' + id + '" class="btn btn-outline-primary">Editar</a>';
        this.type = 'success';
      });
  }

  onSubmit() {
    this.bookService
      .api_post('catalog/itemSearch', this.searchForm.value)
      .subscribe((res) => {
        this.data = res;
        this.itens = this.data.data;
      });
  }
}
