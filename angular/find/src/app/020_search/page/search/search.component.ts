import { Component } from '@angular/core';

@Component({
  selector: 'app-form-search',
  templateUrl: './search.component.html',
  styleUrls: ['./search.component.scss']
})
export class SearchComponent {
  search()
    {
      alert("pesquisar")
    }
}
