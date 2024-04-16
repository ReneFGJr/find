import { FindService } from './../../../000_core/service/find.service';
import { Component } from '@angular/core';

@Component({
  selector: 'app-select',
  templateUrl: './select.component.html'
})
export class SelectLibaryComponent {
  constructor(private findService: FindService) {}

  public libraries: Array<any> | any;

  ngOnInit() {
    console.log('Seleção de Biblioteca');
    this.findService.api_post('library',[]).subscribe((res) => {
      this.libraries = res;
      this.libraries = this.libraries.data;
      console.log(res)
    });
  }
}
