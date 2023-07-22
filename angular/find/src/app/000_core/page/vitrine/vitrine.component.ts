import { Component, Input } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-find-vitrine',
  templateUrl: './vitrine.component.html',
  styleUrls: ['./vitrine.component.scss']
})
export class VitrineComponent {

  constructor(private router: Router) { }

  @Input() public books:Array<any> |any

  goBook(Id:string)
    {
    this.router.navigate(['book/' + Id])
    }

}
