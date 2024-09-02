import { Router } from '@angular/router';
import { FindService } from './../../../000_core/service/find.service';
import { Component } from '@angular/core';
import { LocalStorageService } from 'src/app/000_core/service/local-storage.service';

@Component({
  selector: 'app-select',
  templateUrl: './select.component.html',
})
export class SelectLibaryComponent {
  constructor(
    private findService: FindService,
    private router: Router,
    private localStorageService: LocalStorageService
  ) {}

  public libraries: Array<any> | any;

  ngOnInit() {
    this.findService.api_post('library', []).subscribe((res) => {
      this.libraries = res;
      console.log(res);
    });
  }

  selectLibrary(idLib: string = '') {
    this.findService.setLibrary(idLib);
    this.localStorageService.set('library',idLib)
    //window.location.reload();
    document.location.href = '/find/app/'
    //this.router.navigate(['/']);
  }
}
