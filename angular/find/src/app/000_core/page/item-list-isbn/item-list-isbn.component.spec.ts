import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ItemListIsbnComponent } from './item-list-isbn.component';

describe('ItemListIsbnComponent', () => {
  let component: ItemListIsbnComponent;
  let fixture: ComponentFixture<ItemListIsbnComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ItemListIsbnComponent]
    });
    fixture = TestBed.createComponent(ItemListIsbnComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
