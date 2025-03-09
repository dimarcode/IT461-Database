from app import login
from app import db
from datetime import datetime, timezone
from typing import Optional
import sqlalchemy as sa
import sqlalchemy.orm as so
from werkzeug.security import generate_password_hash, check_password_hash
from flask_login import UserMixin


@login.user_loader
def load_user(id):
    return db.session.get(User, int(id))

class User(UserMixin, db.Model):
    id: so.Mapped[int] = so.mapped_column(primary_key=True)
    username: so.Mapped[str] = so.mapped_column(sa.String(64), index=True,
                                                unique=True)
    email: so.Mapped[str] = so.mapped_column(sa.String(120), index=True,
                                             unique=True)
    password_hash: so.Mapped[Optional[str]] = so.mapped_column(sa.String(256))

    posts: so.WriteOnlyMapped['Post'] = so.relationship(
        back_populates='author')

    def __repr__(self):
        return '<User {}>'.format(self.username)

    def set_password(self, password):
        self.password_hash = generate_password_hash(password)

    def check_password(self, password):
        return check_password_hash(self.password_hash, password)


class Post(db.Model):
    id: so.Mapped[int] = so.mapped_column(primary_key=True)
    body: so.Mapped[str] = so.mapped_column(sa.String(140))
    timestamp: so.Mapped[datetime] = so.mapped_column(
        index=True, default=lambda: datetime.now(timezone.utc))
    user_id: so.Mapped[int] = so.mapped_column(sa.ForeignKey(User.id),
                                               index=True)

    author: so.Mapped[User] = so.relationship(back_populates='posts')

    def __repr__(self):
        return '<Post {}>'.format(self.body)

# class Customer(db.Model): 
#     id: so.Mapped[int] = so.mapped_column(primary_key=True)
#     first_name: so.Mapped[str] = so.mapped_column(sa.String(50))
#     last_name: so.Mapped[str] = so.mapped_column(sa.String(50))
#     address: so.Mapped[Optional[str]] = so.mapped_column(sa.String(100))
#     city: so.Mapped[Optional[str]] = so.mapped_column(sa.String(50))
#     state: so.Mapped[Optional[str]] = so.mapped_column(sa.String(2))
#     zip: so.Mapped[Optional[str]] = so.mapped_column(sa.String(10))
#     phone1: so.Mapped[str] = so.mapped_column(sa.String(15))
#     email: so.Mapped[str] = so.mapped_column(
#         sa.String(100), index=True, unique=True)
    
#     transactions: so.WriteOnlyMapped['Transaction'] = so.relationship(
#         back_populates='customers')

#     def __repr__(self):
#         return '<Customer {}>'.format(self.custID)

# class Transaction(db.Model):
    
#     id: so.Mapped[int] = so.mapped_column(primary_key=True)
#     cust_id: so.Mapped[int] = so.mapped_column(
#         sa.ForeignKey(Customer.id), index=True)
#     date: so.Mapped[datetime] = so.mapped_column(
#         index=True, default=lambda: datetime.now(timezone.utc))
#     total: so.Mapped[float] = so.mapped_column(
#         sa.Numeric(10, 2), nullable=False)
    
#     # Relationship to the Customer table
#     customers: so.WriteOnlyMapped['Customer'] = so.relationship(
#         back_populates='transactions')
    
#     def __repr__(self):
#         return '<transaction {}>'.format(self.transaction_id)

# class Item(db.Model):
#     __tablename__ = 'items'
    
#     itemID: so.Mapped[int] = so.mapped_column(primary_key=True)
#     itemname: so.Mapped[str] = so.mapped_column(sa.String(100), nullable=False, index=True)
#     price: so.Mapped[float] = so.mapped_column(sa.Numeric(10, 2), nullable=False)
    
#     def __repr__(self):
#         return '<Item {}>'.format(self.itemID)
    

    
# class LineItem(db.Model):
#     __tablename__ = 'lineitems'
    
#     lineID: so.Mapped[int] = so.mapped_column(primary_key=True)
#     transaction_id: so.Mapped[int] = so.mapped_column(sa.ForeignKey('transaction.transaction_id'), primary_key=True, index=True)
#     itemID: so.Mapped[int] = so.mapped_column(sa.ForeignKey('items.itemID'), nullable=False, index=True)
#     quantity: so.Mapped[int] = so.mapped_column(sa.Integer, nullable=False)
    
#     # Relationships to the transaction and Item tables
#     transaction: so.Mapped["transaction"] = so.relationship(back_populates="lineitems")
#     item: so.Mapped["Item"] = so.relationship()
    
#     __table_args__ = (
#         sa.UniqueConstraint('lineID', 'transaction_id', name='uix_lineitem_transaction'),
#     )
    
#     def __repr__(self):
#         return '<LineItem {}-{}>'.format(self.transaction_id, self.lineID)