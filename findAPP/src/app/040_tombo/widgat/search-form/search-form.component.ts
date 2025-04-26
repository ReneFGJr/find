import { Component, EventEmitter, Output } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { FindService } from '../../../010_core/service/find.service';

@Component({
  selector: 'app-search-form',
  standalone: false,
  templateUrl: './search-form.component.html',
  styleUrl: './search-form.component.scss',
})
export class SearchFormComponent {
  @Output() searchQuery = new EventEmitter<Event>();
  searchForm: FormGroup;

  constructor(private bookService: FindService) {
    this.searchForm = new FormGroup({
      searchQuery: new FormControl(''),
    });
  }

  onSubmit()
    {
      console.log('Form submitted:', this.searchForm.value);
      this.searchQuery.emit(this.searchForm.value.searchQuery);
    }
}
