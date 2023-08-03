import { Component, Input } from '@angular/core';
import { Router } from '@angular/router';
import { FindService } from 'src/app/000_core/service/find.service';

@Component({
  selector: 'app-preview',
  templateUrl: './preview.component.html',
  styleUrls: ['./preview.component.scss']
})
export class PreviewComponent {

  constructor(
    private findService: FindService,
    private router: Router
  ) { }


  edit_title:boolean = false;
  @Input() public book:Array<any>|any

  edit(field:string)
    {
      if (field=='title')
        {
          this.edit_title = true;
        }
    }

  save(field: string) {
    if (field == 'title') {
      this.edit_title = false;
      alert(this.book.bk_title);
      this.findService.saveData(this.book.isbn, 'bk_title', this.book.bk_title).subscribe(
        res=>
          {
            this.book = res;
          }
      )
    }
  }

  cancel(field: string) {
    if (field == 'title') {
      this.edit_title = false;
    }
  }

}
