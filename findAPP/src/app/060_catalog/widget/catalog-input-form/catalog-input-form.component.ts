import { Component, inject, signal } from '@angular/core';
import { FormArray, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { FindService } from '../../../010_core/service/find.service';

@Component({
  selector: 'app-catalog-input-form',
  standalone: false,
  templateUrl: './catalog-input-form.component.html',
  styleUrl: './catalog-input-form.component.scss',
})
export class CatalogInputFormComponent {
  searchForm: FormGroup;
  data: Array<any> | any;

  constructor(private bookService: FindService) {
    this.searchForm = new FormGroup({
      searchQuery: new FormControl('9788576756316', [Validators.required]),
    });
  }

  onSubmit() {
    this.bookService
      .api_post('catalog/itemSearch', this.searchForm.value)
      .subscribe((res) => {
        this.data = res;
        console.log(this.data);
      });
  }
}
