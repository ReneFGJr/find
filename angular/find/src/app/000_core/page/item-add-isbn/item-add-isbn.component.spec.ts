import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ItemAddIsbnComponent } from './item-add-isbn.component';

describe('ItemAddIsbnComponent', () => {
  let component: ItemAddIsbnComponent;
  let fixture: ComponentFixture<ItemAddIsbnComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ItemAddIsbnComponent]
    });
    fixture = TestBed.createComponent(ItemAddIsbnComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
