import os
from sqlalchemy import create_engine, Column, Integer, String, Text, ForeignKey, MetaData
from sqlalchemy.orm import declarative_base, sessionmaker
from pgvector.sqlalchemy import Vector

# Construct database URL from environment variables
DB_USER = os.getenv("DB_USER", "aura_user")
DB_PASSWORD = os.getenv("DB_PASSWORD", "aura_password")
DB_HOST = os.getenv("DB_HOST", "localhost")  # Fallback to localhost for local testing outside docker
DB_PORT = os.getenv("DB_PORT", "5432")
DB_NAME = os.getenv("DB_NAME", "aura")

DATABASE_URL = f"postgresql://{DB_USER}:{DB_PASSWORD}@{DB_HOST}:{DB_PORT}/{DB_NAME}"

engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

class DocumentChunk(Base):
    __tablename__ = "document_chunks"

    id = Column(Integer, primary_key=True, index=True)
    document_id = Column(Integer, index=True, nullable=False)
    page_number = Column(Integer, nullable=False)
    chunk_index = Column(Integer, nullable=False)
    text = Column(Text, nullable=False)
    # 384 is the dimension for all-MiniLM-L6-v2 model
    embedding = Column(Vector(384))

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()
