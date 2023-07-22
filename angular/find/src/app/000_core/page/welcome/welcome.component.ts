import { Observable } from 'rxjs';
import { Component } from '@angular/core';
import { FindService } from '../../service/find.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-welcome',
  templateUrl: './welcome.component.html',
})
export class WelcomeComponent {
  constructor(
    private findService: FindService,
    private route: ActivatedRoute,
    private router: Router
  ) { }

  public lib:string = '';
  public bookRow:Array<any> |any

  ngOnInit() {
    let lib = this.findService.getLibrary();
    if (lib === '' || lib === '0')
      {
        this.router.navigate(['libraries']);
      }
    this.lib = lib;

    this.findService.vitrine().subscribe(
      res => {
        this.bookRow = res;
        console.log(res)
      }
    );
  }
}
