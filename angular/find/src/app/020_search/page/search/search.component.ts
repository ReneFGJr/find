import { FormControl, FormGroup, Validators } from '@angular/forms';
import { FindService } from './../../../000_core/service/find.service';
import { Component, EventEmitter, Output } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-form-search',
  templateUrl: './search.component.html',
  styleUrls: ['./search.component.scss'],
})
export class SearchComponent {
  @Output() resultSearch = new EventEmitter()
  public meuFormulario: FormGroup | any;
  public data: Array<any> | any;
  public lib: string = '';

  constructor(public findService: FindService, private router: Router) {
    this.meuFormulario = new FormGroup({
      q: new FormControl('', [Validators.required]),
    });
  }

  search() {
    let q = this.meuFormulario.value.q;
    if (q != '') {
      let lib = this.findService.getLibrary();
      if (lib === '' || lib === '0') {
        this.router.navigate(['library']);
      }
      this.lib = lib;
      let dt: Array<any> | any = { q: q, lib: this.lib };
      this.findService.api_post('search/' + lib, dt).subscribe((res) => {
        this.data = res;
        this.resultSearch.emit(this.data);
        console.log(this.data);
      });
    }
  }
}
