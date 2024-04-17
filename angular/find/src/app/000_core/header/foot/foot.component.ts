import { Component } from '@angular/core';
import { LocalStorageService } from '../../service/local-storage.service';

@Component({
  selector: 'app-foot',
  templateUrl: './foot.component.html'
})
export class FootComponent {
  public library: string = '';

  constructor(private localStorageService: LocalStorageService) {}

  ngOnInit() {
    this.library = this.localStorageService.get('library');
  }
}
